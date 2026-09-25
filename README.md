# Arlysere — RDV

Outil interne de suivi d'activité terrain pour travailleurs sociaux. Saisie d'interventions (RDV, Collectif, Événements), filtrage, export Excel, et gestion des listes d'options par un admin.

---

## Prérequis

| Outil | Version minimale |
|-------|-----------------|
| PHP | 8.3+ (extensions : `pdo_mysql`, `mbstring`, `xml`, `curl`, `zip`) |
| Composer | 2.x |
| Node.js | 18+ |
| npm | 9+ |
| MySQL | 8.0+ |

---

## Installation locale (développement)

### 1. Cloner le dépôt

```bash
git clone <url-du-repo> arlysere
cd arlysere
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Éditer `.env` et renseigner la base de données :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arlysere
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 4. Créer la base et migrer

```bash
# Créer la base de données MySQL d'abord :
mysql -u root -p -e "CREATE DATABASE arlysere CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Puis migrer et seeder :
php artisan migrate --seed
```

Le seeder crée deux comptes par défaut :

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Admin | `admin@arlysere.local` | `password` |
| Travailleur | `user@arlysere.local` | `password` |

> **À faire** : changer ces mots de passe immédiatement en production.

### 5. Compiler les assets

```bash
npm run build
```

### 6. Lancer le serveur de développement

```bash
composer run dev
```

L'app est accessible sur `http://localhost:8000`.

---

## Déploiement sur serveur (production)

### Prérequis serveur

- Linux (Ubuntu 22.04+ recommandé)
- PHP 8.3+ avec extensions : `pdo_mysql mbstring xml curl zip opcache`
- Composer, Node.js, npm
- MySQL 8.0+
- Nginx ou Apache
- (Optionnel) Cloudflare Tunnel pour exposer sans port forwarding

### 1. Déposer les fichiers

```bash
git clone <url-du-repo> /var/www/arlysere
cd /var/www/arlysere

composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 2. Configurer l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Renseigner `.env` pour la production :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=arlysere
DB_USERNAME=arlysere_user
DB_PASSWORD=mot_de_passe_fort

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 3. Migrer et seeder

```bash
php artisan migrate --seed --force
```

### 4. Permissions fichiers

```bash
chown -R www-data:www-data /var/www/arlysere
chmod -R 755 /var/www/arlysere/storage
chmod -R 755 /var/www/arlysere/bootstrap/cache
```

### 5. Configurer Nginx

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/arlysere/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Recharger Nginx : `sudo systemctl reload nginx`

### 6. Optimiser pour la production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Mise à jour

Quand une nouvelle version est déployée :

```bash
git pull
composer install --no-dev --optimize-autoloader
npm install && npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## Structure des rôles

| Rôle | Accès |
|------|-------|
| **Travailleur** | Saisie et consultation de ses propres entrées |
| **Admin** | Toutes les entrées + menu admin (options, champs, utilisateurs) |

Un admin peut promouvoir n'importe quel utilisateur via **Admin → Utilisateurs**.

---

## Variables d'environnement clés

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Clé de chiffrement — générer avec `php artisan key:generate` |
| `APP_DEBUG` | `false` en production obligatoirement |
| `DB_*` | Connexion MySQL |
| `SESSION_DRIVER` | `database` recommandé en production |

---

## Commandes utiles

```bash
# Vider tous les caches
php artisan optimize:clear

# Créer un utilisateur admin manuellement
php artisan tinker --execute 'App\Models\User::create(["name"=>"Admin","email"=>"admin@example.com","password"=>bcrypt("password"),"is_admin"=>true]);'

# Voir les routes disponibles
php artisan route:list --except-vendor

# Lancer les tests
php artisan test
```
