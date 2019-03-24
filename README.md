# Salut
An online platform to manage plan, organize and manage events.

## Installation

#### Requirements
- php7
- composer
- mysql
- redis
- openssl

#### Commands to exec

Install all dependencies of the project.
```bash
composer install
```
Generate SSH keys
```bash
mkdir -p config/jwt
```
```bash
openssl genrsa -out config/jwt/private.pem -aes256 4096
```
```bash
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
```yaml
# {project}/config/packages/lexik_jwt_authentication.yaml

lexik_jwt_authentication:
    secret_key: '%env(resolve:JWT_SECRET_KEY)%'
    public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
    pass_phrase: '%env(JWT_PASSPHRASE)%'
    token_ttl: 3600
```
Don't forget to add the variable "JWT_PASSPHRASE" to .env file
```dotenv
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=test123123 # The phrase you entered when generating SSH keys
```