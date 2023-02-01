# Symfony Docker Template

Based on Dunglas' symfony-docker project: https://github.com/dunglas/symfony-docker.

## Installed Bundles
* nelmio/cors-bundle
* symfony/maker-bundle
* symfony/mercure-bundle

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose up`
3. Open `https://localhost` in your web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Trusting the Authority

With a standard installation, the authority used to sign certificates generated in the Caddy container is not trusted by your local machine.
You must add the authority to the trust store of the host :

```
# Mac
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
# Linux
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
# Windows
$ docker compose cp caddy:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```
