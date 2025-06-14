
# Ponto API
### Passo a passo
Clone Repositório
```sh
git clone https://github.com/ElMarcelFarias/pontoapi-laravel10
```
```sh
cd pontoapi-laravel10
```


Crie o Arquivo .env
```sh
cp .env.example .env
```


Atualize as variáveis de ambiente do arquivo .env
```dosini
APP_NAME="pontoapi"
APP_URL=http://localhost:8989

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=pontoapi
DB_USERNAME=root
DB_PASSWORD=root

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```


Suba os containers do projeto
```sh
docker-compose up -d
```


Acesse o container app
```sh
docker-compose exec app bash
```


Instale as dependências do projeto
```sh
composer install
```

🔐 JWT Authentication (Instale o pacote JWT_
```sh
composer require tymon/jwt-auth
```

Publique a configuração
```sh
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
```

Gere o secret JWT
```sh
php artisan jwt:secret
```

🧾 Geração de PDF (Instale o DomPDF)
```sh
composer require barryvdh/laravel-dompdf
```

12. Crie o link do storage
```sh
php artisan storage:link
```

14. Crie a pasta para relatórios PDF
```sh
mkdir exports
chmod -R 775 exports
```

✅ Acesse a API
[http://localhost:8989](http://localhost:8989)

📌 Observações
O sistema utiliza Laravel 10, PHP 8.1, MySQL, Redis e JWT.

As rotas da API estão documentadas em Documentacao Rotas Api.

Para acessar as rotas protegidas, envie o header:

```sh
Authorization: Bearer <seu_token_jwt>
```

Gere a key do projeto Laravel
```sh
php artisan key:generate
```

