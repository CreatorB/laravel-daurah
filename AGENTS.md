# AGENTS.md - Daurah Laravel Project

## SSH Access
- **Host**: 45.130.231.127
- **Port**: 65002
- **User**: u4486592
- **Key**: `C:\msys64\home\ZDK\.ssh\id_rsa`

## Quick SSH Command
```powershell
& "C:\msys64\usr\bin\ssh.exe" -p 65002 -i "C:\msys64\home\ZDK\.ssh\id_rsa" -o StrictHostKeyChecking=no -o ServerAliveInterval=60 u4486592@45.130.231.127
```

## Server Paths
- **Document Root**: `/home/u4486592/public_html/daurah.syathiby.id`
- **Storage**: `/home/u4486592/public_html/daurah.syathiby.id/storage/app/public`

## Maintenance Endpoints
- `https://daurah.syathiby.id/maintenance/clear-view?key=Syathiby@756`
- `https://daurah.syathiby.id/maintenance/db-status?key=Syathiby@756`

## File Serving Route
- `/file/bukti-undangan/{filename}` - serves files from storage/app/public/bukti_undangan/