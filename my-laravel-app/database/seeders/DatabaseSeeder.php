<?php

namespace Database\Seeders;

use App\Models\Birka;
use App\Models\Kategorija;
use App\Models\Komentars;
use App\Models\Raksts;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Lomas ────────────────────────────────────────────────
        $roles = collect(['viesis', 'lietotajs', 'autors', 'moderators', 'administrators'])
            ->map(fn ($n) => Role::firstOrCreate(['nosaukums' => $n]));

        $roleAutors       = $roles->firstWhere('nosaukums', 'autors');
        $roleAdmins       = $roles->firstWhere('nosaukums', 'administrators');
        $roleLietotajs    = $roles->firstWhere('nosaukums', 'lietotajs');

        // ── Kategorijas ───────────────────────────────────────────
        $kategorijas = [
            ['nosaukums' => 'Tehnoloģijas',       'slug' => 'tehnologijas',       'krasa' => '#1565C0'],
            ['nosaukums' => 'Personīgā izaugsme', 'slug' => 'personiga-izaugsme', 'krasa' => '#2E7D32'],
            ['nosaukums' => 'Mārketings',         'slug' => 'marketings',         'krasa' => '#AD1457'],
            ['nosaukums' => 'Ceļojumi',           'slug' => 'celojumi',           'krasa' => '#E65100'],
            ['nosaukums' => 'Veselība',           'slug' => 'veseliba',           'krasa' => '#1B5E20'],
        ];
        foreach ($kategorijas as $k) {
            Kategorija::firstOrCreate(['slug' => $k['slug']], $k);
        }
        $katTeh  = Kategorija::where('slug', 'tehnologijas')->first();
        $katIzau = Kategorija::where('slug', 'personiga-izaugsme')->first();
        $katMark = Kategorija::where('slug', 'marketings')->first();
        $katCel  = Kategorija::where('slug', 'celojumi')->first();
        $katVes  = Kategorija::where('slug', 'veseliba')->first();

        // ── Birkas ────────────────────────────────────────────────
        $birkaData = [
            'laravel', 'vue-js', 'php', 'javascript',
            'latvija', 'celojumi', 'motivacija', 'veseliba',
            'sports', 'saturs', 'marketings', 'digitalizacija',
        ];
        foreach ($birkaData as $slug) {
            Birka::firstOrCreate(
                ['slug' => $slug],
                ['nosaukums' => Str::title(str_replace('-', '.', $slug))]
            );
        }
        $bLaravel = Birka::where('slug', 'laravel')->first();
        $bVue     = Birka::where('slug', 'vue-js')->first();
        $bLatvija = Birka::where('slug', 'latvija')->first();
        $bCel     = Birka::where('slug', 'celojumi')->first();
        $bMotiv   = Birka::where('slug', 'motivacija')->first();
        $bVes     = Birka::where('slug', 'veseliba')->first();
        $bSports  = Birka::where('slug', 'sports')->first();
        $bSaturs  = Birka::where('slug', 'saturs')->first();
        $bMark    = Birka::where('slug', 'marketings')->first();
        $bDigit   = Birka::where('slug', 'digitalizacija')->first();

        // ── Lietotāji ─────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@bloghub.lv'],
            ['name' => 'BlogHub Admins', 'password' => Hash::make('password'), 'role_id' => $roleAdmins->id]
        );
        $anna = User::firstOrCreate(
            ['email' => 'anna@bloghub.lv'],
            ['name' => 'Anna Kalniņa', 'password' => Hash::make('password'), 'role_id' => $roleAutors->id,
             'bio' => 'Tehnoloģiju entuziaste un full-stack izstrādātāja. Rakstu par Laravel, Vue.js un mūsdienu tīmekļa izstrādi.']
        );
        $maris = User::firstOrCreate(
            ['email' => 'maris@bloghub.lv'],
            ['name' => 'Māris Bērziņš', 'password' => Hash::make('password'), 'role_id' => $roleAutors->id,
             'bio' => 'Personīgās izaugsmes coaches un motivācijas autors. Palīdzu cilvēkiem atrast savu ceļu.']
        );
        $zane = User::firstOrCreate(
            ['email' => 'zane@bloghub.lv'],
            ['name' => 'Zane Liepiņa', 'password' => Hash::make('password'), 'role_id' => $roleAutors->id]
        );
        $janis = User::firstOrCreate(
            ['email' => 'janis@bloghub.lv'],
            ['name' => 'Jānis Ozoliņš', 'password' => Hash::make('password'), 'role_id' => $roleAutors->id]
        );
        $inga = User::firstOrCreate(
            ['email' => 'inga@bloghub.lv'],
            ['name' => 'Inga Saulīte', 'password' => Hash::make('password'), 'role_id' => $roleAutors->id]
        );
        $kristine = User::firstOrCreate(
            ['email' => 'kristine@bloghub.lv'],
            ['name' => 'Kristīne Vītoliņa', 'password' => Hash::make('password'), 'role_id' => $roleLietotajs->id]
        );

        // ── Raksti ────────────────────────────────────────────────
        $postsData = [
            [
                'user_id'          => $anna->id,
                'category_id'      => $katTeh->id,
                'virsraksts'       => 'Kā izveidot personīgu emuāru 2025. gadā',
                'slug'             => 'ka-izveidot-emuaru-2025',
                'ievads'           => 'Šajā rakstā aplūkojam labākās platformas un padomus kā sākt rakstīt emuāru Latvijā — no idejas līdz pirmajai publikācijai.',
                'saturs'           => '<p>Emuāra izveide 2025. gadā ir vieglāka nekā jebkad agrāk. Ar tādām platformām kā BlogHub, Wordpress un Ghost, ikviens var sākt rakstīt dažu minūšu laikā.</p><p>Svarīgākais ir izvēlēties sev piemērotāko platformu un sākt rakstīt regulāri.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 342,
                'publicets_datums' => now()->subDays(12),
                'birkas'           => [$bLaravel->id, $bVue->id],
            ],
            [
                'user_id'          => $maris->id,
                'category_id'      => $katIzau->id,
                'virsraksts'       => 'Rakstīšanas ieradumu veidošana',
                'slug'             => 'rakstisanas-paradumi',
                'ievads'           => 'Kā veidot konsekventus rakstīšanas paradumus un uzlabot sava satura kvalitāti. Praktiski padomi no pieredzējušiem bloggeriem.',
                'saturs'           => '<p>Regulāra rakstīšana ir prasme, ko var izkopt. Sāciet ar 15 minūtēm dienā un pakāpeniski palieliniet laiku.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 217,
                'publicets_datums' => now()->subDays(17),
                'birkas'           => [$bMotiv->id],
            ],
            [
                'user_id'          => $zane->id,
                'category_id'      => $katMark->id,
                'virsraksts'       => 'Autentiskuma nozīme saturā',
                'slug'             => 'autentiskums-saturaa',
                'ievads'           => 'Kāpēc autentisks saturs uzvar pār perfektu saturu — lasītāju viedokļi, reāla pieredze un atklāsmju kopīgošana.',
                'saturs'           => '<p>Autentiskums ir kļuvis par galveno faktoru satura mārketingā. Lasītāji vēlas dzirdēt reālus stāstus, nevis polētus reklāmas tekstus.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 189,
                'publicets_datums' => now()->subDays(22),
                'birkas'           => [$bSaturs->id, $bMark->id],
            ],
            [
                'user_id'          => $janis->id,
                'category_id'      => $katTeh->id,
                'virsraksts'       => 'Latvijas digitālā transformācija',
                'slug'             => 'latvija-digitala-transformacija',
                'ievads'           => 'Kā Latvija kļūst par vienu no digitāli attīstītākajām valstīm Eiropā — e-pārvalde, jaunuzņēmumi un tehnoloģiju ekosistēma.',
                'saturs'           => '<p>Latvija ir veikusi nozīmīgus soļus digitalizācijas jomā. No e-veselības līdz e-vēlēšanām — mūsu valsts ir priekšgalā daudzās jomās.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 445,
                'publicets_datums' => now()->subDays(29),
                'birkas'           => [$bLatvija->id, $bDigit->id],
            ],
            [
                'user_id'          => $inga->id,
                'category_id'      => $katCel->id,
                'virsraksts'       => 'Labākās vietas ceļošanai Latvijā',
                'slug'             => 'celosana-latvija',
                'ievads'           => 'No Siguldas pilsdrupām līdz Ventspils pludmalēm — iepazīsti Latvijas skaistumu četros gadalaikos.',
                'saturs'           => '<p>Latvija piedāvā neticamu daudzveidību — no senčiem veltītas pilsētas Cēsīm līdz Piejūras nacionālajam parkam. Katrs gadalaiks atnes ko jaunu un iedvesmojošu.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 612,
                'publicets_datums' => now()->subDays(37),
                'birkas'           => [$bLatvija->id, $bCel->id],
            ],
            [
                'user_id'          => $kristine->id,
                'category_id'      => $katVes->id,
                'virsraksts'       => 'Veselīga uzturēšanās ikdienā',
                'slug'             => 'vesela-uztursanas',
                'ievads'           => 'Vienkārši padomi kā uzturēt veselīgu dzīvesveidu pat visaizņemtākajās dienās — uzturs, kustība un miegs.',
                'saturs'           => '<p>Veselīgs dzīvesveids nenozīmē radikālas pārmaiņas. Sāciet ar maziem soļiem — vairāk ūdens, regulāras pastaigas un pietiekams miegs.</p>',
                'statuss'          => 'publicets',
                'skatijumi'        => 298,
                'publicets_datums' => now()->subDays(42),
                'birkas'           => [$bVes->id, $bSports->id],
            ],
        ];

        foreach ($postsData as $data) {
            $birkas = $data['birkas'];
            unset($data['birkas']);

            $raksts = Raksts::firstOrCreate(['slug' => $data['slug']], $data);
            $raksts->tags()->sync($birkas);
        }

        // ── Komentāri ─────────────────────────────────────────────
        $pirmaisRaksts = Raksts::where('slug', 'ka-izveidot-emuaru-2025')->first();
        if ($pirmaisRaksts) {
            $comments = [
                ['user_id' => $maris->id, 'saturs' => 'Ļoti noderīgs raksts! Pats tikko sāku savu emuāru.', 'apstiprints' => true],
                ['user_id' => $zane->id,  'saturs' => 'Paldies par detalizētajiem padomiem.',               'apstiprints' => true],
                ['user_id' => $admin->id, 'saturs' => 'Lieliski! Ieteikšu draugiem.',                        'apstiprints' => true],
            ];
            foreach ($comments as $c) {
                Komentars::firstOrCreate(
                    ['post_id' => $pirmaisRaksts->id, 'user_id' => $c['user_id']],
                    array_merge($c, ['post_id' => $pirmaisRaksts->id])
                );
            }
        }
    }
}
