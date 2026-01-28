# Caddy (xcaddy custom) + CrowdSec (LAPI + AppSec) – Docker stack

Dette oppsettet bygger en custom Caddy-binær med xcaddy-modulene:
- github.com/sjtug/caddy2-filter
- github.com/caddy-dns/cloudflare
- github.com/hslatman/caddy-crowdsec-bouncer/http
- github.com/hslatman/caddy-crowdsec-bouncer/appsec

Og kjører CrowdSec i egen container (LAPI + AppSec), med collections installert ved oppstart.

## Struktur
- build/Dockerfile.caddy        -> bygger custom Caddy
- deploy/compose.yaml           -> kjører hele stacken
- deploy/Caddyfile              -> eksempel-konfig (MÅ endres til ditt domene)
- deploy/crowdsec/acquis.d/*    -> acquis for caddy + appsec
- deploy/.env.example           -> kopier til deploy/.env

## 1) Forbered
Gå til deploy-mappen og lag .env:

```bash
cd deploy
cp .env.example .env
nano .env
```

Oppdater `your.domain.tld` i `deploy/Caddyfile` til ditt faktiske domene.

## 2) Start stacken (bygger Caddy-image første gang)
```bash
docker compose up -d --build
```

## 3) Generer CrowdSec bouncer key (én gang)
Kjør:

```bash
docker compose exec crowdsec cscli bouncers add caddyDmz
```

Kopier nøkkelen som skrives ut og sett den inn i `deploy/.env` som `CROWDSEC_API_TOKEN=...`.

Restart Caddy:

```bash
docker compose restart caddy
```

## 4) Sjekk status
```bash
docker compose logs -f caddy
docker compose exec crowdsec cscli collections list
docker compose exec crowdsec cscli metrics
```

## Notater
- CrowdSec leser Caddy-logger fra et delt Docker-volume (`caddy_logs`).
- AppSec må lytte på 0.0.0.0:7422 i containeren for at Caddy skal nå den.
