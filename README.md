# Symfony Docker SSE Demo (Backend)
This demo works in tandem with [VUE Docker SSE Demo](https://github.com/J-C-V/vue-docker-sse-demo). Based on 
[Dunglas' symfony-docker project](https://github.com/dunglas/symfony-docker) with added Microsoft SQL Server support. 
There is no stable Microsoft SQL Server PHP driver support for PHP 8.2 yet, so the template is currently using 
PHP 8.1. Initial Microsoft SQL Server setup is based on https://github.com/twright-msft/mssql-node-docker-demo-app.

This demo showcases how to utilize the [Mercure protocol](https://symfony.com/doc/current/mercure.html) to push data
in real time to clients, as well as persisting data to a Microsoft SQL Server database using Doctrine.
Just send a POST request to the `messages` endpoint with the `message` property in the request body to publish it to 
all subscribers and save it to the database.

## Getting Started
1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose up`
3. Open `https://localhost` in your web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
4. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Demo Endpoints
All endpoints are reachable from localhost.
Request body has to be JSON.

| Endpoint  | Method | Request Body Parameters | Description                 |
|-----------|--------|-------------------------|-----------------------------|
| /         | GET    | -                       | phpinfo                     |
| /messages | POST   | message                 | Publish and store a message | 
| /messages | GET    | -                       | Get the message history     |

## TLS Certificates and Security Concerns
With a standard installation, the authority used to sign certificates generated in the Caddy container is not trusted by
your local machine. You must add the authority to the trust store of the host :
```
# Mac
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /tmp/root.crt && sudo security add-trusted-cert -d -r trustRoot -k /Library/Keychains/System.keychain /tmp/root.crt
# Linux
$ docker cp $(docker compose ps -q caddy):/data/caddy/pki/authorities/local/root.crt /usr/local/share/ca-certificates/root.crt && sudo update-ca-certificates
# Windows
$ docker compose cp caddy:/data/caddy/pki/authorities/local/root.crt %TEMP%/root.crt && certutil -addstore -f "ROOT" %TEMP%/root.crt
```

`TrustServerCertificate` in `config/packages/doctrine.yaml` is currently set to true in Doctrine's sqlsrv driver 
options. With this option set, [the transport layer will use SSL to encrypt the channel but will bypass the certificate 
chain to validate trust](https://learn.microsoft.com/en-us/dotnet/api/system.data.sqlclient.sqlconnectionstringbuilder.trustservercertificate?view=dotnet-plat-ext-7.0)
(See also [this question on stackoverflow.com](https://stackoverflow.com/a/71735233)).

## Accessing the Demo from an External Device
The external device has to be on the same local network without valid certificates. Be aware that this bypasses HTTPS 
security mechanisms because certificates aren't validated. Only use that for testing in local development!

The demo has to be configured as such:
1. Set the server name to `https://` in`.env` to allow any https connection connect to the Caddy server.
2. Set CORS options in `.env` to allow accessing it from another origin. This is normally your local host IP.
3. Set `MERCURE_PUBLIC_URL` and `MERCURE_TOPIC_URL` in `.env` accordingly.
4. Disable `verify_host` and `verify_peer` in `config/packages/framework.yaml`.
5. Enable [on-demand TLS](https://caddyserver.com/docs/automatic-https#on-demand-tls) in `docker/caddy/Caddyfile`.
6. Open your host IP on the external device and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
