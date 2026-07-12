#!/bin/bash
# Cloudflare Tunnel setup pakai TOKEN (tanpa cert.pem)
# Usage: bash setup-tunnel-token.sh <TOKEN>

set -e

TUNNEL_NAME="syathiby-tunnel"
CLOUDFLARED="$HOME/bin/cloudflared"
CONFIG_DIR="$HOME/.cloudflared"
TOKEN="$1"

if [ -z "$TOKEN" ]; then
    echo "Usage: bash setup-tunnel-token.sh <TOKEN>"
    exit 1
fi

echo "=== [1/7] Decode JWT ==="
# JWT format: header.payload.signature (base64url)
PAYLOAD=$(echo "$TOKEN" | cut -d. -f2)
# Add padding & convert base64url -> base64
PAD=$(( 4 - ${#PAYLOAD} % 4 ))
[ $PAD -ne 4 ] && PAYLOAD="$PAYLOAD$(printf '=%.0s' $(seq 1 $PAD))"
PAYLOAD_B64=$(echo "$PAYLOAD" | tr '_-' '/+')
DECODED=$(echo "$PAYLOAD_B64" | base64 -d 2>/dev/null)
echo "Decoded payload: $DECODED"

ACCOUNT_TAG=$(echo "$DECODED" | python3 -c "import sys,json; print(json.load(sys.stdin)['a'])")
TUNNEL_SECRET=$(echo "$DECODED" | python3 -c "import sys,json; print(json.load(sys.stdin)['s'])" | base64 -d 2>/dev/null || echo "$DECODED" | python3 -c "import sys,json,base64; print(base64.b64decode(json.load(sys.stdin)['s']).decode())")
TUNNEL_ID=$(echo "$DECODED" | python3 -c "import sys,json; print(json.load(sys.stdin)['t'])")

echo "  AccountTag    : $ACCOUNT_TAG"
echo "  TunnelSecret  : $TUNNEL_SECRET"
echo "  TunnelID      : $TUNNEL_ID"

echo ""
echo "=== [2/7] Write credentials JSON ==="
mkdir -p "$CONFIG_DIR"
cat > "$CONFIG_DIR/$TUNNEL_ID.json" <<EOF
{
  "AccountTag": "$ACCOUNT_TAG",
  "TunnelSecret": "$TUNNEL_SECRET",
  "TunnelID": "$TUNNEL_ID"
}
EOF
chmod 600 "$CONFIG_DIR/$TUNNEL_ID.json"
ls -la "$CONFIG_DIR/$TUNNEL_ID.json"

echo ""
echo "=== [3/7] Write config.yml ==="
cat > "$CONFIG_DIR/config.yml" <<EOF
tunnel: $TUNNEL_ID
credentials-file: $CONFIG_DIR/$TUNNEL_ID.json
metrics: localhost:2000
no-autoupdate: true

ingress:
  - hostname: syathibyonline.com
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: www.syathibyonline.com
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: syathiby.com
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: smail.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: psb.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: mukholif.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: aplikasi.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: dev-aplikasi.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: mobile.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: daurah.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: data.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: rodja.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: sabaqin.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: sabaqina.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: food.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - hostname: icall.syathiby.id
    service: https://localhost:443
    originRequest: { noTLSVerify: true }
  - service: http_status:404
EOF
echo "OK"
head -5 "$CONFIG_DIR/config.yml"
echo "... (18 ingress rules)"
tail -3 "$CONFIG_DIR/config.yml"

echo ""
echo "=== [4/7] Validate config ==="
$CLOUDFLARED tunnel --config "$CONFIG_DIR/config.yml" ingress validate 2>&1 | tail -5

echo ""
echo "=== [5/7] Test connect to origin (local) ==="
curl -ksS -o /dev/null -w 'Origin: HTTP=%{http_code} TIME=%{time_total}\n' https://localhost:443 --max-time 5

echo ""
echo "=== [6/7] Run tunnel (background) ==="
# Kill existing tunnel if any
pkill -f "cloudflared.*tunnel run" 2>/dev/null || true
sleep 2

nohup $CLOUDFLARED --config "$CONFIG_DIR/config.yml" tunnel run $TUNNEL_NAME > /tmp/cf-tunnel.log 2>&1 &
TUNNEL_PID=$!
echo "Tunnel PID: $TUNNEL_PID"
sleep 8

echo ""
echo "=== Status ==="
ps -ef | grep cloudflared | grep -v grep
echo ""
echo "=== Log ==="
tail -30 /tmp/cf-tunnel.log

echo ""
echo "=== [7/7] Auto-restart (crontab @reboot) ==="
CRON_CMD="@reboot sleep 15 && $CLOUDFLARED --config $CONFIG_DIR/config.yml tunnel run $TUNNEL_NAME >> /tmp/cf-tunnel.log 2>&1"
( crontab -l 2>/dev/null | grep -v "cloudflared.*tunnel run" ; echo "$CRON_CMD" ) | crontab -
crontab -l | grep cloudflared

echo ""
echo "=== TUNNEL ACTIVE? ==="
sleep 5
$CLOUDFLARED --config "$CONFIG_DIR/config.yml" tunnel info $TUNNEL_NAME 2>&1 | tail -10

echo ""
echo "=== DONE ==="
echo ""
echo "SELANJUTNYA (manual di dashboard Cloudflare):"
echo "1. Buka https://one.dash.cloudflare.com/ > Zero Trust > Networks > Tunnels"
echo "2. Klik tunnel 'syathiby-tunnel' > tab 'Public Hostname'"
echo "3. Tambah 18 public hostname:"
echo "   - subdomain: (kosong) | domain: syathibyonline.com | service: https://localhost:443"
echo "   - subdomain: www | domain: syathibyonline.com | service: https://localhost:443"
echo "   - subdomain: (kosong) | domain: syathiby.com | service: https://localhost:443"
echo "   - subdomain: (kosong) | domain: syathiby.id | service: https://localhost:443"
echo "   - subdomain: smail | domain: syathiby.id | service: https://localhost:443"
echo "   - dst untuk: psb, mukholif, aplikasi, dev-aplikasi, mobile, daurah,"
echo "                data, rodja, sabaqin, sabaqina, food, icall"
echo ""
echo "Setelah semua Public Hostname ditambah, test:"
echo "  curl -ksS https://daurah.syathiby.id/login --max-time 15"