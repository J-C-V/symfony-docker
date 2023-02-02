# Symfony Docker Demo
Based on Dunglas' symfony-docker project: https://github.com/dunglas/symfony-docker with added Microsoft SQL Server support.
There is no stable Microsoft SQL Server PHP driver support for PHP 8.2 yet, so the template is currently using PHP 8.1.
Initial Microsoft SQL Server setup is based on
https://github.com/twright-msft/mssql-node-docker-demo-app.

This demo showcases how to utilize the [Mercure protocol](https://symfony.com/doc/current/mercure.html) to push data
in real time to clients.

## Getting Started
1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose up`
3. Open `https://localhost` in your web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker compose down --remove-orphans` to stop the Docker containers.

## TLS Certificates
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
`TrustServerCertificate` is also currently set to true in Doctrine's sqlsrv driver options. With this option set,
[the transport layer will use SSL to encrypt
the channel but will bypass the certificate chain to validate trust](https://learn.microsoft.com/en-us/dotnet/api/system.data.sqlclient.sqlconnectionstringbuilder.trustservercertificate?view=dotnet-plat-ext-7.0)
(See also [this question on stackoverflow.com](https://stackoverflow.com/a/71735233)).
