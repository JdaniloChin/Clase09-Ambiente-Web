<#!
.SYNOPSIS
 Automatiza creación y merge de Pull Request desde 'develop' hacia 'main'.
.DESCRIPTION
 Requiere GitHub CLI (gh) autenticado. Crea PR si no existe y lo fusiona automáticamente.
.PARAMETER Title
 Título del Pull Request.
.PARAMETER Body
 Descripción/cuerpo del Pull Request.
.PARAMETER Squash
 Usa squash merge.
.PARAMETER Rebase
 Usa rebase merge.
.PARAMETER NoAuto
 No activa merge automático (--auto) y fusiona inmediatamente.
.PARAMETER DryRun
 Muestra acciones sin ejecutarlas.
.PARAMETER Force
 Añade flags administrativos (si aplica) al merge.
.EXAMPLE
 ./pr-merge.ps1 -Title "Actualización" -Body "Cambios" -Squash
.EXAMPLE
 ./pr-merge.ps1 -DryRun
.NOTES
 Autor: Script generado por asistente.
!>
[CmdletBinding()] Param(
  [string]$Title = "Actualización desde develop",
  [string]$Body = "Merge automático de develop a main",
  [switch]$Squash,
  [switch]$Rebase,
  [switch]$NoAuto,
  [switch]$DryRun,
  [switch]$Force
)

function Write-Info($msg){ Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Warn($msg){ Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg){ Write-Host "[ERROR] $msg" -ForegroundColor Red }
function Exec($cmd){
  Write-Info $cmd
  if($DryRun){ return }
  $out = & $cmd 2>&1
  if($LASTEXITCODE -ne 0){ Write-Err $out; throw "Fallo ejecutando: $cmd" }
  return $out
}

# Validaciones
if (-not (Get-Command git -ErrorAction SilentlyContinue)) { Write-Err "Git no está instalado."; exit 1 }
if (-not (Get-Command gh -ErrorAction SilentlyContinue)) { Write-Err "GitHub CLI 'gh' no está instalado. Instala: winget install GitHub.cli"; exit 1 }

# Verificar autenticación gh
try { $authStatus = gh auth status 2>&1 } catch { $authStatus = $_ }
if($authStatus -match "You are not logged") { Write-Err "No autenticado en GitHub CLI. Ejecuta: gh auth login"; exit 1 }

# Asegurar rama develop local
$current = (git rev-parse --abbrev-ref HEAD).Trim()
if($current -ne "develop"){
  Write-Info "Cambiando a rama develop"
  Exec { git checkout develop }
}

Exec { git fetch origin }
Exec { git pull origin develop }
Exec { git push origin develop }

Write-Info "Buscando PR existente de develop -> main"
$prJson = gh pr list --head develop --base main --json number,state,title 2>$null
$prNumber = ""
if($prJson){
  try { $parsed = $prJson | ConvertFrom-Json } catch { $parsed = @() }
  if($parsed -and $parsed.Count -gt 0){ $prNumber = $parsed[0].number }
}

if(-not $prNumber){
  Write-Info "No existe PR. Creando uno nuevo."
  $createCmd = "gh pr create --base main --head develop --title `"$Title`" --body `"$Body`""
  Exec { $createCmd }
  # Obtener número nuevamente
  $prJson = gh pr list --head develop --base main --json number 2>$null
  if($prJson){ $prNumber = (ConvertFrom-Json $prJson)[0].number }
}
if(-not $prNumber){ Write-Err "No se pudo obtener número de PR."; exit 1 }
Write-Info "PR #$prNumber listo."

# Determinar estrategia de merge
$mergeFlag = "--merge"
if($Squash){ $mergeFlag = "--squash" }
elseif($Rebase){ $mergeFlag = "--rebase" }

$autoFlag = if($NoAuto) { "" } else { "--auto" }
$deleteFlag = "--delete-branch"
$forceFlag = if($Force){ "--admin" } else { "" }

Write-Info "Aplicando merge (modo: $mergeFlag)"
$mergeCmd = "gh pr merge $prNumber $mergeFlag $autoFlag $deleteFlag $forceFlag".Trim()
Exec { $mergeCmd }

Write-Info "Proceso finalizado. Revisa estado con: gh pr view $prNumber"
