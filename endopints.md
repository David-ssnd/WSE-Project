# 📘 API Endpointy

> [!NOTE]
> Pri každej ceste je použité `/api/`.
> Neviem či je to vhodné, ale videl som to použité inde. Možno je to dobré, aby ľudia nedokázali len tak otvoriť stránku, ktorá im vypíše json response.

> [!WARNING]
> Nie všetky funkcionality sú implementované na frontende, treba dorobiť.

## 🔐 Autentifikácia
- `POST /api/auth/register` – Registrácia používateľa  
- `POST /api/auth/login` – Prihlásenie a získanie tokenu  
- `POST /api/auth/logout` – Odhlásenie používateľa  
- `GET /api/profile` – získať profil aktuálneho používateľa  
- `PATCH /api/profile` – upraviť profil
###
- `GET /api/user/{id}` – získať verejný profil iného používateľa  
- `POST /api/user/{id}/follow` – sledovať používateľa  
- `DELETE /api/user/{id}/unfollow` – prestať sledovať používateľa  
- `GET /api/user/{id}/followers` – získať zoznam followerov  
- `GET /api/user/{id}/following` – získať zoznam sledovaných  

## 🍽️ Recepty
- `GET /api/recipes` – Získanie zoznamu receptov + ingrediencii do preview 
- `GET /api/recipes/{id}` – Získanie detailov receptu  
- `POST /api/recipes` – Vytvorenie nového receptu  
- `PATCH /api/recipes/{id}` – Úprava receptu  
- `DELETE /api/recipes/{id}` – Odstránenie receptu  

## 🥕 Ingrediencie
- `GET /api/ingredients` – Získanie zoznamu ingrediencií  
- `GET /api/ingredients/{id}` – Získanie detailu ingrediencie  
- `POST /api/ingredients` – Pridanie novej ingrediencie  
- `PATCH /api/ingredients/{id}` – Úprava ingrediencie  
- `DELETE /api/ingredients/{id}` – Odstránenie ingrediencie  

## ⭐ Hodnotenia a Komentáre
- `POST /api/recipes/{id}/rate` – Ohodnotenie receptu  
- `GET /api/recipes/{id}/ratings` – Získanie hodnotení receptu  
- `POST /api/recipes/{id}/comments` – Pridanie komentára k receptu  
- `GET /api/recipes/{id}/comments` – Získanie komentárov receptu  
- `DELETE /api/comments/{id}` – Odstránenie komentára  

## ❤️ Obľúbené Recepty
- `POST /api/recipes/{id}/favorite` – Pridanie receptu do obľúbených  
- `DELETE /api/recipes/{id}/favorite` – Odstránenie receptu z obľúbených  
- `GET /api/user/favorites` – Získanie obľúbených receptov používateľa  


## 🔎 Vyhľadávanie a Filtrovanie
- `GET /api/search?query={text}` – Vyhľadávanie receptov  
- `GET /api/recipes?ingredient={id}&&tag={id}` – Filtrovanie receptov podľa kategórie a ingrediencie 