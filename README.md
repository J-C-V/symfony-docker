# Symfony-Docker-SSE-Demo
This demo works in tandem with [Vue-Docker-SSE-Demo](https://github.com/J-C-V/vue-docker-sse-demo). Based on 
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
3. Open `http://localhost` to see the demo
4. Run `docker compose down --remove-orphans` to stop the Docker containers

## Demo Endpoints
All endpoints are reachable from localhost.
Request body has to be JSON.

| Endpoint  | Method | Request Body Parameters | Description                 |
|-----------|--------|-------------------------|-----------------------------|
| /         | GET    | -                       | phpinfo                     |
| /messages | POST   | message                 | Publish and store a message | 
| /messages | GET    | -                       | Get the message history     |

## TrustedServerCertificate
In this demo `TrustServerCertificate` in `config/packages/doctrine.yaml` is currently set to true in Doctrine's sqlsrv driver 
options. With this option set, [the transport layer will use SSL to encrypt the channel but will bypass the certificate 
chain to validate trust](https://learn.microsoft.com/en-us/dotnet/api/system.data.sqlclient.sqlconnectionstringbuilder.trustservercertificate?view=dotnet-plat-ext-7.0)
(See also [this question on stackoverflow.com](https://stackoverflow.com/a/71735233)).

## Debugging on Mobile Devices

### Android
You can remote debug and test on Android devices with the help of [Google Chrome's DevTools](https://developer.chrome.com/docs/devtools/remote-debugging/). 
To test the demo enable [port-forwarding in the remote device settings](https://developer.chrome.com/docs/devtools/remote-debugging/local-server/) 
and you're good to go! 

Be sure to set `MERCURE_PUBLIC_URL`, `MERCURE_TOPIC_URL` and `MERCURE_CORS_ORIGIN` in your `.env` to the forwarded port 
and valid frontend URL as well (See [Vue-Docker-SSE-Demo](https://github.com/J-C-V/vue-docker-sse-demo)).

### iOS
WIP
