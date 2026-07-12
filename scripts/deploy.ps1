#Requires -Version 5.1
<#
.SYNOPSIS
    Full-automation deploy script for daurah.syathiby.id production.
.DESCRIPTION
    Loads task definitions from deploy.manifest.json and deploys selected files
    via SCP. Supports auto-detection of modified files via git status.
    Does NOT push to git - user controls git operations manually.
.PARAMETER Task
    Comma-separated task names from manifest.
.PARAMETER Files
    Explicit file paths (legacy mode, bypasses manifest).
.PARAMETER All
    Deploy every task in manifest.
.PARAMETER Auto
    Auto-detect modified files via git status.
.PARAMETER List
    List all tasks defined in manifest and exit.
.PARAMETER DryRun
    Preview without changes.
.PARAMETER Force
    Skip confirmation prompt.
.PARAMETER Smoke
    URL to fetch after deploy for smoke testing.
.PARAMETER Manifest
    Path to manifest JSON. Default: scripts\deploy.manifest.json
.EXAMPLE
    .\deploy.ps1 -Auto
.EXAMPLE
    .\deploy.ps1 -Task login
.EXAMPLE
    .\deploy.ps1 -All -Force
.EXAMPLE
    .\deploy.ps1 -Auto -Smoke "https://daurah.syathiby.id/login"
.EXAMPLE
    .\deploy.ps1 -List
#>
param(
    [string]$Task,
    [string[]]$Files = @(),
    [switch]$All,
    [switch]$Auto,
    [switch]$List,
    [switch]$DryRun,
    [switch]$Force,
    [string]$Smoke,
    [string]$Manifest
)
$ErrorActionPreference = "Stop"
$ScriptDir   = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir
if (-not $Manifest) { $Manifest = Join-Path $ScriptDir "deploy.manifest.json" }
Set-Location $ProjectRoot
$Config = @{
    SshHost    = "45.130.231.127"
    SshPort    = "65002"
    SshUser    = "u4486592"
    SshKey     = "C:\msys64\home\ZDK\.ssh\id_rsa"
    SshBinary  = "C:\msys64\usr\bin\ssh.exe"
    ScpBinary  = "C:\msys64\usr\bin\scp.exe"
    RemoteRoot = "/home/u4486592/public_html/daurah.syathiby.id"
    TmpDir     = Join-Path $env:TEMP "daurah-deploy"
    LogFile    = Join-Path $env:TEMP "daurah-deploy\deploy-$(Get-Date -Format 'yyyyMMdd-HHmmss').log"
}
if (-not (Test-Path $Config.TmpDir)) { New-Item -ItemType Directory -Path $Config.TmpDir | Out-Null }
function Write-Log { param([string]$Message, [string]$Level = "INFO")
    $line = "[$(Get-Date -Format 'HH:mm:ss')] [$Level] $Message"
    Add-Content -Path $Config.LogFile -Value $line -ErrorAction SilentlyContinue
}
function Write-Step { param($m) Write-Host "`n===> $m" -ForegroundColor Cyan;  Write-Log $m "STEP" }
function Write-Ok   { param($m) Write-Host "  [OK] $m" -ForegroundColor Green;  Write-Log $m "OK" }
function Write-Warn { param($m) Write-Host "  [!]  $m" -ForegroundColor Yellow; Write-Log $m "WARN" }
function Write-Err  { param($m) Write-Host "  [X]  $m" -ForegroundColor Red;     Write-Log $m "ERROR" }
function Write-Info { param($m) Write-Host "      $m";                            Write-Log $m "INFO" }

# ===== SSH / SCP with retry =====
function Invoke-Ssh {
    param([string]$Command, [switch]$Quiet)
    $target = "$($Config.SshUser)@$($Config.SshHost)"
    $outFile = Join-Path $Config.TmpDir "ssh-out.txt"
    $errFile = Join-Path $Config.TmpDir "ssh-err.txt"
    $attempts = 5; $delaySec = 3; $lastStderr = ""
    for ($i = 1; $i -le $attempts; $i++) {
        Remove-Item $outFile, $errFile -ErrorAction SilentlyContinue
        $proc = Start-Process -FilePath $Config.SshBinary             -ArgumentList @("-T","-p",$Config.SshPort,"-i",$Config.SshKey,"-o","StrictHostKeyChecking=no","-o","BatchMode=yes","-o","ServerAliveInterval=15","-o","ServerAliveCountMax=4","-o","ConnectTimeout=10",$target,$Command) -NoNewWindow -Wait -PassThru -RedirectStandardOutput $outFile -RedirectStandardError $errFile
        $stdout = if (Test-Path $outFile) { (Get-Content $outFile -Raw -ErrorAction SilentlyContinue) } else { "" }
        $stderr = if (Test-Path $errFile) { (Get-Content $errFile -Raw -ErrorAction SilentlyContinue) } else { "" }
        if ($null -eq $stdout) { $stdout = "" }
        if ($null -eq $stderr) { $stderr = "" }
        $lastStderr = $stderr
        if ($proc.ExitCode -eq 0) { return $stdout.Trim() }
        if ($i -lt $attempts) { Write-Warn "SSH attempt $i/$attempts failed, retrying in ${delaySec}s..."; Start-Sleep -Seconds $delaySec }
    }
    if (-not $Quiet) { throw "SSH failed after $attempts attempts. Command: $Command`n$lastStderr" }
    return ""
}

function Invoke-Scp {
    param([string]$Local, [string]$Remote)
    $target = "$($Config.SshUser)@$($Config.SshHost):$Remote"
    $outFile = Join-Path $Config.TmpDir "scp-out.txt"
    $errFile = Join-Path $Config.TmpDir "scp-err.txt"
    $attempts = 5; $delaySec = 3; $lastStderr = ""
    for ($i = 1; $i -le $attempts; $i++) {
        Remove-Item $outFile, $errFile -ErrorAction SilentlyContinue
        $proc = Start-Process -FilePath $Config.ScpBinary -ArgumentList @("-P",$Config.SshPort,"-i",$Config.SshKey,"-o","StrictHostKeyChecking=no","-o","ServerAliveInterval=15","-o","ServerAliveCountMax=4","-o","ConnectTimeout=10",$Local,$target) -NoNewWindow -Wait -PassThru -RedirectStandardOutput $outFile -RedirectStandardError $errFile
        if ($proc.ExitCode -eq 0) { return }
        $stderr = if (Test-Path $errFile) { (Get-Content $errFile -Raw -ErrorAction SilentlyContinue) } else { "" }
        if ($null -eq $stderr) { $stderr = "" }
        $lastStderr = $stderr
        if ($i -lt $attempts) { Write-Warn "SCP attempt $i/$attempts failed, retrying in ${delaySec}s..."; Start-Sleep -Seconds $delaySec }
    }
    throw "SCP failed after $attempts attempts for $Local -> $Remote`n$lastStderr"
}

function Get-RemotePath { param([string]$Local) return "$($Config.RemoteRoot)/$($Local -replace '\\','/')" }

function Resolve-FileList {
    param([string[]]$Patterns)
    $resolved = @()
    foreach ($p in $Patterns) {
        if ($p -match '[\*\?]') {
            $found = Get-ChildItem -Path $p -Recurse -File -ErrorAction SilentlyContinue | Select-Object -ExpandProperty FullName
            foreach ($f in $found) {
                $rel = $f.Substring($ProjectRoot.Length).TrimStart('\','/') -replace '\\','/'
                $resolved += $rel
            }
        } else {
            $resolved += ($p -replace '\\','/')
        }
    }
    return $resolved | Select-Object -Unique
}

# ===== Load manifest =====
function Load-Manifest {
    param([string]$Path)
    if (-not (Test-Path $Path)) {
        Write-Err "Manifest not found: $Path"
        exit 1
    }
    try {
        $raw = Get-Content -Raw -Path $Path | ConvertFrom-Json
    } catch {
        Write-Err "Manifest JSON is invalid: $_"
        exit 1
    }
    return $raw
}

function Show-TaskList {
    param($ManifestData)
    Write-Host "`nAvailable tasks in $Manifest" -ForegroundColor Cyan
    Write-Host ("-" * 60)
    foreach ($key in ($ManifestData.tasks.PSObject.Properties | ForEach-Object { $_.Name })) {
        $t = $ManifestData.tasks.$key
        $fileCount = if ($t.files) { @($t.files).Count } else { 0 }
        $fileList = if ($t.files) {
            $shown = ($t.files | Select-Object -First 2) -join ', '
            $extra = if ($fileCount -gt 2) { " (+$($fileCount - 2) more)" } else { "" }
            "$shown$extra"
        } else { "" }
        Write-Host ("  {0,-15} {1}" -f $key, $t.name) -ForegroundColor White
        if ($t.description) { Write-Host ("    {0}" -f $t.description) -ForegroundColor Gray }
        Write-Host ("    files: {0}" -f $fileList) -ForegroundColor DarkGray
    }
    Write-Host ""
}

# ===== Auto-detect from git =====
function Get-GitModifiedFiles {
    $status = & git status --porcelain 2>$null
    if (-not $status) { return @() }
    $files = @()
    foreach ($line in $status) {
        if ($line.Length -lt 4) { continue }
        $path = $line.Substring(3).Trim()
        if ($path.Contains(" -> ")) {
            $path = $path.Split(" -> ")[1]
        }
        if (Test-Path -LiteralPath $path -PathType Leaf) {
            $files += ($path -replace '\\','/')
        }
    }
    return $files | Select-Object -Unique
}

function Find-Tasks-ForFiles {
    param($ManifestData, [string[]]$Files)
    $matched = @{}
    $taskNames = $ManifestData.tasks.PSObject.Properties | ForEach-Object { $_.Name }
    foreach ($taskName in $taskNames) {
        $patterns = @($ManifestData.tasks.$taskName.files)
        $resolved = Resolve-FileList -Patterns $patterns
        $hits = @()
        foreach ($f in $Files) {
            if ($resolved -contains $f) { $hits += $f }
        }
        if ($hits.Count -gt 0) {
            $matched[$taskName] = $hits
        }
    }
    return $matched
}

# ===== Deploy a single task =====
function Invoke-DeployTask {
    param([string]$TaskName, $TaskDef, [string[]]$Files, [bool]$IsDryRun)
    Write-Step "Task: $TaskName - $($TaskDef.name)"
    if ($TaskDef.description) { Write-Info $TaskDef.description }

    $resolved = Resolve-FileList -Patterns @($TaskDef.files)

    $filesToDeploy = if ($Files.Count -gt 0) {
        $intersect = @()
        foreach ($f in $Files) { if ($resolved -contains $f) { $intersect += $f } }
        $intersect
    } else { $resolved }

    if ($filesToDeploy.Count -eq 0) {
        Write-Warn "No files to deploy for task '$TaskName'."
        return
    }

    $totalSize = 0
    foreach ($f in $filesToDeploy) {
        $localFull = Join-Path $ProjectRoot ($f -replace '/','\')
        if (Test-Path -LiteralPath $localFull) {
            $size = (Get-Item -LiteralPath $localFull).Length
            $totalSize += $size
            Write-Info "$f ($size bytes)"
        } else {
            Write-Warn "Local file missing: $f"
        }
    }
    Write-Info "Total: $($filesToDeploy.Count) files, $totalSize bytes"

    if ($IsDryRun) {
        Write-Warn "DRY-RUN: skipping backup/upload/verify for task '$TaskName'"
        return
    }

    $ts = Get-Date -Format "yyyyMMdd-HHmmss"
    foreach ($f in $filesToDeploy) {
        $remote = Get-RemotePath $f
        try {
            $result = Invoke-Ssh "test -f '$remote' && echo exists || echo missing"
            if ($result -eq "missing") {
                Write-Ok "New file: $f"
            } else {
                Invoke-Ssh "cp '$remote' '$remote.bak-$ts'" -Quiet | Out-Null
                Write-Ok "Backed up: $f"
            }
        } catch {
            Write-Warn "Could not backup $f (SSH flaky); proceeding with upload"
        }
    }

    foreach ($f in $filesToDeploy) {
        $remote = Get-RemotePath $f
        $remoteDir = Split-Path -Parent $remote
        Invoke-Ssh "mkdir -p '$remoteDir'" -Quiet | Out-Null
        Invoke-Scp -Local (Join-Path $ProjectRoot ($f -replace '/','\')) -Remote $remote
        Write-Ok "Uploaded: $f"
    }

    if ($TaskDef.postCommands) {
        foreach ($cmd in $TaskDef.postCommands) {
            Write-Info "post: $cmd"
            try { Invoke-Ssh $cmd -Quiet | Out-Null } catch { Write-Warn "Post-command failed: $cmd" }
        }
    }

    foreach ($f in $filesToDeploy) {
        $remote = Get-RemotePath $f
        $localSize = (Get-Item -LiteralPath (Join-Path $ProjectRoot ($f -replace '/','\'))).Length
        try {
            $remoteSize = Invoke-Ssh "stat -c %s '$remote'"
            if ([int]$remoteSize -eq $localSize) {
                Write-Ok "Verified ($remoteSize bytes): $f"
            } else {
                Write-Warn "Size mismatch: $f (local=$localSize, remote=$remoteSize)"
            }
        } catch {
            Write-Warn "Could not verify $f (SSH flaky); upload likely succeeded"
        }
    }
}

function Test-Smoke {
    param([string]$Url)
    Write-Step "Smoke test: $Url"
    try {
        $r = Invoke-WebRequest -UseBasicParsing -Uri $Url -TimeoutSec 30
        $code = $r.StatusCode
        $len = $r.Content.Length
        if ($code -ge 200 -and $code -lt 400) {
            Write-Ok "HTTP $code, $len bytes"
            return $true
        } else {
            Write-Err "HTTP $code"
            return $false
        }
    } catch {
        Write-Err "Smoke test failed: $($_.Exception.Message)"
        return $false
    }
}

# ===== Main flow =====
$manifestData = Load-Manifest -Path $Manifest

if ($List) {
    Show-TaskList -ManifestData $manifestData
    exit 0
}

$selectedTasks = [ordered]@{}

if ($Task) {
    $names = $Task -split ',' | ForEach-Object { $_.Trim() } | Where-Object { $_ }
    foreach ($n in $names) {
        $t = $manifestData.tasks.$n
        if (-not $t) {
            Write-Err "Task not found in manifest: $n"
            Show-TaskList -ManifestData $manifestData
            exit 1
        }
        $selectedTasks[$n] = @{ Def = $t; Files = @() }
    }
} elseif ($Files.Count -gt 0) {
    $selectedTasks["(ad-hoc)"] = @{ Def = @{ name = "Ad-hoc files"; files = $Files }; Files = $Files }
} elseif ($All) {
    foreach ($key in ($manifestData.tasks.PSObject.Properties | ForEach-Object { $_.Name })) {
        $selectedTasks[$key] = @{ Def = $manifestData.tasks.$key; Files = @() }
    }
} else {
    $Auto = $true
}

if ($Auto) {
    Write-Step "Auto-detecting modified files (git status)"
    $modified = Get-GitModifiedFiles
    if ($modified.Count -eq 0) {
        Write-Warn "No modified files detected by git."
        Write-Info "Hint: stage/commit changes first, or use -Task/-Files/-All to deploy explicitly."
        Show-TaskList -ManifestData $manifestData
        exit 0
    }
    Write-Info "Modified files: $($modified.Count)"
    foreach ($m in $modified) { Write-Info "  $m" }
    $matched = Find-Tasks-ForFiles -ManifestData $manifestData -Files $modified
    if ($matched.Count -eq 0) {
        Write-Warn "No manifest task matches the modified files."
        Write-Info "Either add the files to a task in $Manifest, or deploy explicitly with -Files."
        exit 0
    }
    foreach ($key in $matched.Keys) {
        $selectedTasks[$key] = @{ Def = $manifestData.tasks.$key; Files = $matched[$key] }
    }
}

# ===== Summary =====
Write-Step "Deploy plan"
Write-Host ("-" * 60)
foreach ($key in $selectedTasks.Keys) {
    $entry = $selectedTasks[$key]
    $def = $entry.Def
    Write-Host ("  [{0}] {1}" -f $key, $def.name) -ForegroundColor White
    $files = if ($entry.Files.Count -gt 0) { $entry.Files } else { Resolve-FileList -Patterns @($def.files) }
    foreach ($f in $files) { Write-Host ("      - {0}" -f $f) -ForegroundColor Gray }
}
Write-Host ("-" * 60)

if ($DryRun) {
    Write-Warn "DRY-RUN MODE - nothing will be uploaded"
}

if (-not $DryRun -and -not $Force) {
    Write-Host ""
    $answer = Read-Host "Proceed? [y/N]"
    if ($answer -notmatch '^[Yy]([Ee][Ss])?$') {
        Write-Warn "Cancelled by user."
        exit 0
    }
}

# ===== Preflight: SSH =====
Write-Step "Preflight: SSH connection"
try {
    $hello = Invoke-Ssh "echo connected"
    Write-Ok "SSH OK ($hello)"
} catch {
    Write-Err "Cannot connect to SSH. Aborting."
    exit 1
}

# ===== Deploy each task =====
foreach ($key in $selectedTasks.Keys) {
    $entry = $selectedTasks[$key]
    Invoke-DeployTask -TaskName $key -TaskDef $entry.Def -Files $entry.Files -IsDryRun:$DryRun
}

# ===== Global post commands (e.g., cache clear) =====
if (-not $DryRun -and $manifestData.global -and $manifestData.global.postCommands) {
    Write-Step "Global post-deploy commands"
    foreach ($cmd in $manifestData.global.postCommands) {
        Write-Info "$cmd"
        try { Invoke-Ssh $cmd -Quiet | Out-Null; Write-Ok "OK" } catch { Write-Warn "Failed (non-fatal): $cmd" }
    }
}

# ===== Smoke test =====
if ($Smoke -and -not $DryRun) {
    Test-Smoke -Url $Smoke | Out-Null
}

# ===== Done =====
Write-Host ""
if ($DryRun) {
    Write-Host "DRY-RUN complete (no changes made)." -ForegroundColor Yellow
} else {
    Write-Host "Deployment completed." -ForegroundColor Green
}
Write-Host "Log file: $($Config.LogFile)" -ForegroundColor DarkGray
Write-Host ""