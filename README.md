# 🎭 Grappenschrijver

Een moderne, interactieve webapplicatie om geleide grappenmaken te leren via een 5-staps proces.

## 📋 Kenmerken

- **Home Pagina**: Toont alle gepubliceerde grappen uit de database
- **Interactieve Workshop**: 5-staps proces om grappen te creëren
  1. **Intro**: Uitleg van het proces (optioneel)
  2. **Stap 1**: Betekenissen invoeren voor willekeurige woorden
  3. **Stap 2**: Associaties invoeren per betekenis
  4. **Stap 3**: Daadwerkelijke grappen schrijven
  5. **Stap 4**: Grappen bekijken en selectief publiceren
- **Modern ontwerp**: Responsief design met gradient kleuren en smooth animations
- **Database-gestuurde**: Alle gegevens opgeslagen in MySQL
- **Sessie-bewustzijn**: Houdt je voortgang bij met unieke sessie-identifiers

## 🛠️ Vereisten

- PHP 7.4 of hoger
- MySQL 5.7 of hoger
- Webserver (Apache/Nginx)
- Lokale webserver (XAMPP, WAMP, MAMP) of extern gehost

## 📦 Instalatie

### 1. Database opzetten

```sql
-- Open MySQL en voer uit:
mysql -u root -p < includes/database.sql
```

Of via phpMyAdmin:
1. Ga naar phpMyAdmin
2. Importeer het bestand `includes/database.sql`
3. Vul je databasegegevens in `includes/config.php` in

### 2. Configuratie aanpassen

**Bewerk `includes/config.php`:**
```php
define('DB_HOST', 'localhost');    // Je database host
define('DB_USER', 'root');         // Je database gebruiker
define('DB_PASS', '');             // Je database wachtwoord
define('DB_NAME', 'grappenschrijver');  // Database naam
```

### 3. Webserver configureren

**Voor Apache (htaccess):**
Zorg dat `mod_rewrite` is ingeschakeld.

**Voor XAMPP/WAMP:**
- Plaats de map in `htdocs` of `www`
- Toegang via `http://localhost/grappenschrijver`

### 4. Bestandspermissies

Zorg dat volgende mappen schrijfrechten hebben:
```bash
chmod -R 755 /path/to/grappenschrijver
chmod -R 777 /path/to/grappenschrijver/assets
```

## 🚀 Gebruik

1. **Start de webserver**
2. Ga naar `http://localhost/grappenschrijver`
3. Klik op "Begin het Proces" tab
4. Volg alle 5 stappen
5. Bekijk je gepubliceerde grappen op de home pagina

## 📁 Projectstructuur

```
grappenschrijver/
├── index.php                 # Home pagina
├── assets/
│   ├── css/
│   │   └── style.css        # Alle styling
│   └── js/
│       └── main.js          # JavaScript interactiviteit
├── includes/
│   ├── config.php           # Database configuratie
│   ├── database.sql         # Database schema
│   ├── header.php           # Header template
│   └── footer.php           # Footer template
└── pages/
    ├── intro.php            # Introductie pagina
    ├── step1.php            # Stap 1: Betekenissen
    ├── process_step1.php    # Verwerking stap 1
    ├── step2.php            # Stap 2: Associaties
    ├── process_step2.php    # Verwerking stap 2
    ├── step3.php            # Stap 3: Grappen schrijven
    ├── process_step3.php    # Verwerking stap 3
    └── review.php           # Stap 4: Review & publiceren
```

## 🎨 Ontwerp Features

- **Moderne kleuren**: Paarse en roze gradients
- **Responsief**: Werkt perfect op mobiel, tablet en desktop
- **Accessibility**: Goede contrast en grote clickable areas
- **Animaties**: Smooth transitions en hover effects
- **Step counter**: Visuele progressie door het proces

## 🔒 Veiligheid

- Prepared statements voor SQL injection-bescherming
- HTML escaping voor XSS-bescherming
- Sessie management voor gebruikersgegevens
- CSRT-bescherming kan later worden toegevoegd

## 🎓 Woordenlijst toevoegen

Bij het installeren krijg je 10 voorbeeldwoorden. Voeg meer toe via:

```sql
INSERT INTO words (word, category) VALUES ('JouwWoord', 'Categorie');
```

## 🐛 Troubleshooting

**"Database connection error"**
- Controleer MySQL service is actief
- Verificeer je credentials in `config.php`

**"Headers already sent"**
- Zorg dat `session_start()` zich bovenin het bestand bevindt
- Geen output voor headers!

**"404 Page not found"**
- Controleer je webserver root path
- Zorg dat de URLs correct zijn ingesteld

## 📝 Toekomstige Verbeteringen

- [ ] User accounts & inloggen
- [ ] Grappen ratings/likes
- [ ] Categorieën filter
- [ ] Export naar PDF
- [ ] Social sharing
- [ ] Admin panel

## 📄 Licentie

Dit project is vrij te gebruiken voor persoonlijke en educatieve doeleinden.

## 💬 Suggesties?

Heb je feedback of suggesties? Stuur een bericht!

---

**Geniet van je grappenmakerswerkshop! 😂**
