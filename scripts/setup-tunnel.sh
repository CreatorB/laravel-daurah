#!/bin/bash
# Cloudflare Tunnel setup script untuk server 45.130.231.127
# Jalankan sebagai user u4486592 di server origin
# Usage: bash setup-tunnel.sh

set -e

TUNNEL_NAME="syathiby-tunnel"
CLOUDFLARED="$HOME/bin/cloudflared"
CONFIG_DIR="$HOME/.cloudflared"

echo "=== [1/8] Cek cloudflared ==="
if [ ! -x "$CLOUDFLARED" ]; then
    echo "Installing cloudflared..."
    mkdir -p ~/bin
    curl -sL -o ~/bin/cloudflared https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-amd64
    chmod +x ~/bin/cloudflared
fi
$CLOUDFLARED --version

echo ""
echo "=== [2/8] Cek cert.pem ==="
mkdir -p "$CONFIG_DIR"
if [ ! -f "$CONFIG_DIR/cert.pem" ]; then
    echo "cert.pem belum ada. Generate ulang..."
    echo ""
    echo "LANGKAH:"
    echo "1. Copy URL di bawah ini"
    echo "2. Buka di browser"
    echo "3. Authorize + pilih domain syathibyonline.com"
    echo "4. Browser akan download cert.pem"
    echo "5. Upload ke server: scp cert.pem u4486592@45.130.231.127:~/.cloudflared/cert.pem"
    echo ""
    nohup $CLOUDFLARED tunnel login > /tmp/cf-login.log 2>&1 &
    sleep 5
    cat /tmp/cf-login.log | grep -oE 'https://[^ ]+' | head -1
    echo ""
    echo "Setelah cert.pem di-upload, jalankan ulang script ini."
    exit 1
fi
echo "OK: $CONFIG_DIR/cert.pem"

echo ""
echo "=== [3/8] Create tunnel ==="
if [ ! -f "$CONFIG_DIR/$TUNNEL_NAME.json" ]; then
    $CLOUDFLARED tunnel create $TUNNEL_NAME
fi
TUNNEL_ID=$($CLOUDFLARED tunnel list | grep "$TUNNEL_NAME" | awk '{print $1}')
echo "Tunnel ID: $TUNNEL_ID"

echo ""
echo "=== [4/8] Route DNS (18 domain) ==="
DOMAINS=(
    "syathibyonline.com" "www.syathibyonline.com"
    "syathiby.com" "syathiby.id"
    "smail.syathiby.id" "psb.syathiby.id" "mukholif.syathiby.id"
    "aplikasi.syathiby.id" "dev-aplikasi.syathiby.id" "mobile.syathiby.id"
    "daurah.syathiby.id" "data.syathiby.id" "rodja.syathiby.id"
    "sabaqin.syathiby.id" "sabaqina.syathiby.id" "food.syathiby.id"
    "icall.syathiby.id"
)
for d in "${DOMAINS[@]}"; do
    echo "  route: $d"
    $CLOUDFLARED tunnel route dns $TUNNEL_NAME "$d" 2>&1 | tail -1
done

echo ""
echo "=== [5/8] Tulis config.yml ==="
cat > "$CONFIG_DIR/config.yml" <<EOF
tunnel: $TUNNEL_ID
credentials-file: $CONFIG_DIR/$TUNNEL_NAME.json
metrics: localhost:2000
no-autoupdate: true

ingress:
  - hostname: syathibyonline.com
    service: https://localhost:443
    originRequest:
      noTLSVerify: true
      connectTimeout: 30s
      tlsTimeout: 30s
  - hostname: www.syathibyonline.com
    service: https://localhost:443
    originRequest:
      noTLSVerify: true
  - hostname: syathiby.com
    service: https://localhost:443
    originRequest:
      noTLSVerify: true
  - hostname: syathiby.id
    service: https://localhost:443
    originRequest:
      noTLSVerify: true
EOF
for d in "${DOMAINS[@]:4}"; do
cat >> "$CONFIG_DIR/config.yml" <<EOF
  - hostname: $d
    service: https://localhost:443
    originRequest:
      noTLSVerify: true
EOF
done
cat >> "$CONFIG_DIR/config.yml" <<EOF

  - service: http_status:404
EOF
echo "OK"

echo ""
echo "=== [6/8] Validasi config ==="
$CLOUDFLARED tunnel --config $CONFIG_DIR/config.yml ingress validate

echo ""
echo "=== [7/8] Test run (10 detik) ==="
echo "Jalankan manual untuk test:"
echo "  $CLOUDFLARED --config $CONFIG_DIR/config.yml tunnel run $TUNNEL_NAME"
echo ""
echo "Atau langsung ke step 8 (install service) jika Anda sudah yakin."

echo ""
echo "=== [8/8] Setup auto-restart (no sudo, pakai nohup + crontab) ==="
# Karena tidak ada sudo, kita pakai crontab @reboot
CRON_CMD="@reboot sleep 10 && $CLOUDFLARED --config $CONFIG_DIR/config.yml tunnel run $TUNNEL_NAME >> /tmp/cf-tunnel.log 2>&1"
( crontab -l 2>/dev/null | grep -v "cloudflared.*tunnel run" ; echo "$CRON_CMD" ) | crontab -
echo "OK: crontab @reboot installed"

echo ""
echo "=== START SEKARANG ==="
$CLOUDFLARED --config $CONFIG_DIR/config.yml tunnel run $TUNNEL_NAME &
sleep 5
ps -ef | grep cloudflared | grep -v grep
echo ""
echo "=== TEST ==="
sleep 3
curl -ksS -o /dev/null -w 'HTTP=%{http_code} TIME=%{time_total}\n' https://daurah.syathiby.id/login --max-time 15
curl -ksS -o /dev/null -w 'HTTP=%{http_code} TIME=%{time_total}\n' https://smail.syathiby.id/ --max-time 15

echo ""
echo "=== DONE ==="
echo "Tunnel ID: $TUNNEL_ID"
echo "Cek di: https://one.dash.cloudflare.com/ > Zero Trust > Networks > Tunnels"
echo "Log: /tmp/cf-tunnel.log"