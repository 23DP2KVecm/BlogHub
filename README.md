# BlogHub — Emuāru Platforma

BlogHub ir mūsdienīga tīmekļa vietne emuāru rakstīšanai un lasīšanai latviešu valodā. Sistēma ļauj lietotājiem reģistrēties, publicēt rakstus, komentēt un atrast interesantu saturu pēc kategorijām.

---

## Tehnoloģijas

| Puse | Tehnoloģija |
|------|-------------|
| Frontend | Vue.js 3 + Vuetify 3 (Material Design) |
| Backend | Laravel 12 (PHP 8.2+) |
| Datubāze | MySQL |
| Veidošanas rīks | Vite 7 |
| Valoda | TypeScript (frontend) |
| Pakotņu pārvaldnieks | pnpm (frontend) / Composer (backend) |

---

## Projekta struktūra

```
BlogHub/
├── vuetify-project/     # Vue.js + Vuetify SPA (frontend)
│   └── src/
│       ├── pages/       # Lapas (auto-routing)
│       ├── components/  # Atkārtoti lietojami komponenti
│       ├── composables/ # Kompozīcijas funkcijas (API, auth)
│       └── plugins/     # Vuetify konfigurācija
└── my-laravel-app/      # Laravel REST API (backend)
    ├── app/
    │   ├── Models/      # Eloquent modeļi
    │   └── Http/Controllers/
    ├── database/
    │   ├── migrations/  # Datubāzes migrācijas
    │   └── seeders/     # Testa dati
    └── routes/
        └── api.php
```

---

## Datubāzes tabulas

| Tabula | Apraksts |
|--------|----------|
| `roles` | Lietotāju lomas (viesis, lietotājs, autors, moderators, administrators) |
| `users` | Reģistrētie lietotāji ar lomas FK un API tokenu |
| `kategorijas` | Rakstu kategorijas ar krāsu kodiem |
| `birkas` | Tematiskās birkas |
| `raksti` | Emuāru raksti ar statusu un skatījumiem |
| `komentari` | Rakstu komentāri ar apstiprināšanu |
| `reakcijas` | Patīk/nepatīk reakcijas uz rakstiem |
| `raksts_birka` | N:M starptabula starp rakstiem un birkām |

---

## Lietotāju lomas

| Loma | Tiesības |
|------|----------|
| Viesis | Lasa publicētos rakstus un kategorijas |
| Lietotājs | Pieslēdzas, atstāj komentārus |
| Autors | Izveido, rediģē un dzēš savus rakstus |
| Moderators | Apstiprina komentārus |
| Administrators | Pārvalda visu sistēmu |

---

## Demo lietotāji

| E-pasts | Parole | Loma |
|---------|--------|------|
| admin@bloghub.lv | password | Administrators |
| anna@bloghub.lv | password | Autors |
| maris@bloghub.lv | password | Autors |
| zane@bloghub.lv | password | Lietotājs |
| janis@bloghub.lv | password | Lietotājs |

---

## Palaišana izstrādē

### Priekšnosacījumi
- PHP 8.2+, Composer
- Node.js 18+, pnpm
- XAMPP (Apache + MySQL)

### Datubāze

1. Palaid XAMPP → Start **Apache** un **MySQL**
2. Atver `http://localhost/phpmyadmin`
3. Izveido datubāzi ar nosaukumu `bloghub`

### Backend (Laravel)

```bash
cd my-laravel-app
composer install
php artisan migrate:fresh --seed
php artisan serve      # http://localhost:8000
```

### Frontend (Vuetify)

```bash
cd vuetify-project
pnpm install
pnpm run dev           # http://localhost:3000
```

Frontend automātiski pāradresē `/api/*` pieprasījumus uz `http://localhost:8000`.

---

## API galapunkti

### Publiskie

| Metode | Ceļš | Apraksts |
|--------|------|----------|
| GET | `/api/posts` | Rakstu saraksts (filtrēšana, kārtošana, lapošana) |
| GET | `/api/posts/{slug}` | Viens raksts ar komentāriem |
| GET | `/api/categories` | Kategoriju saraksts ar rakstu skaitu |
| GET | `/api/tags` | Birku saraksts |
| GET | `/api/stats` | Platformas statistika |
| POST | `/api/auth/register` | Reģistrācija |
| POST | `/api/auth/login` | Pieslēgšanās |

### Autorizētie (Bearer token)

| Metode | Ceļš | Apraksts |
|--------|------|----------|
| POST | `/api/auth/logout` | Atslēgšanās |
| GET | `/api/auth/me` | Pašreizējais lietotājs |
| GET | `/api/dashboard/posts` | Mani raksti |
| POST | `/api/dashboard/posts` | Izveidot rakstu |
| PUT | `/api/dashboard/posts/{id}` | Rediģēt rakstu |
| DELETE | `/api/dashboard/posts/{id}` | Dzēst rakstu |

### Administratora

| Metode | Ceļš | Apraksts |
|--------|------|----------|
| GET | `/api/admin/users` | Visi lietotāji |
| PUT | `/api/admin/users/{id}/role` | Mainīt lietotāja lomu |
| GET | `/api/admin/roles` | Lomu saraksts |
| GET | `/api/admin/posts` | Visi raksti |
| DELETE | `/api/admin/posts/{id}` | Dzēst rakstu |
| GET | `/api/admin/comments` | Visi komentāri |
| PATCH | `/api/admin/comments/{id}/approve` | Apstiprināt/atcelt komentāru |
| DELETE | `/api/admin/comments/{id}` | Dzēst komentāru |

### Filtrēšanas parametri `GET /api/posts`

| Parametrs | Vērtības | Apraksts |
|-----------|---------|----------|
| `kartojums` | `jaunakie`, `vecakie`, `popularakie` | Kārtošana |
| `kategorija` | kategorijas ID | Filtrēšana pēc kategorijas |
| `meklet` | teksts | Meklēšana virsrakstā un ievadā |
| `datums_no` | YYYY-MM-DD | Filtrēšana no datuma |
| `datums_lidz` | YYYY-MM-DD | Filtrēšana līdz datumam |
| `min_skatijumi` | skaitlis | Minimālais skatījumu skaits |
| `lapa` | skaitlis | Lapas numurs (6 raksti lapā) |

---

## Autors

**Klāvs Vecmanis**

Izstrādāts kā noslēguma darbs — Rīgas Valsts Tehnikums, Datorika, Programmēšana, 2025./2026. m.g.
