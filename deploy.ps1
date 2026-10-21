# deploy.ps1 — Tag a commit so GitHub Actions tests it, builds the Docker image and pushes it
# to ghcr.io/gheith3/enjaz (.github/workflows/build-and-push.yml).
#
# Usage:
#   .\deploy.ps1                      # prompts: release (patch/minor/major) or test build
#   .\deploy.ps1 -Bump patch          # v1.2.3 -> v1.2.4   (image tags 1.2.4, 1.2, latest)
#   .\deploy.ps1 -Bump minor          # v1.2.3 -> v1.3.0
#   .\deploy.ps1 -Bump major          # v1.2.3 -> v2.0.0
#   .\deploy.ps1 -Version 1.5.0       # exact version
#   .\deploy.ps1 -Build               # test image build-YYYYMMDD-HHMM (does not move `latest`)
#   .\deploy.ps1 -Bump patch -Yes -Watch   # no confirmation, then follow the GitHub run
#
# Releases are only tagged from an up-to-date, clean `main` that is pushed to origin, so the
# image is always built from code that is on GitHub and has passed review.
param(
    [ValidateSet("patch", "minor", "major", "")]
    [string]$Bump = "",

    [string]$Version = "",

    [switch]$Build,

    [string]$Message = "",

    [switch]$Yes,

    [switch]$Watch
)

$ErrorActionPreference = "Stop"
$Repo = "gheith3/enjaz"
$ReleaseBranch = "main"

function Fail([string]$text) {
    Write-Host "✗ $text" -ForegroundColor Red
    exit 1
}

function Invoke-Git([string[]]$arguments) {
    $output = & git.exe @arguments 2>&1
    if ($LASTEXITCODE -ne 0) { Fail "git $($arguments -join ' ') failed:`n$output" }
    return $output
}

# --- Where are we? -----------------------------------------------------------------
Set-Location (Invoke-Git @("rev-parse", "--show-toplevel"))
$branch = (Invoke-Git @("rev-parse", "--abbrev-ref", "HEAD")).Trim()

Write-Host "Fetching tags and $ReleaseBranch from origin..." -ForegroundColor Cyan
Invoke-Git @("fetch", "--tags", "--quiet", "origin") | Out-Null

# --- What to tag ---------------------------------------------------------------------
if (-not $Build -and [string]::IsNullOrEmpty($Bump) -and [string]::IsNullOrEmpty($Version)) {
    Write-Host "What do you want to deploy?" -ForegroundColor Cyan
    Write-Host "  1. Release - patch (bug fixes)"
    Write-Host "  2. Release - minor (new features)"
    Write-Host "  3. Release - major (breaking changes)"
    Write-Host "  4. Test build (build-*, does not move 'latest')"
    do { $choice = Read-Host "Enter number (1-4)" } until ($choice -match '^[1-4]$')
    switch ($choice) {
        "1" { $Bump = "patch" }
        "2" { $Bump = "minor" }
        "3" { $Bump = "major" }
        "4" { $Build = $true }
    }
}

# Latest release tag vX.Y.Z (ignores build-* and anything not semver).
$latest = Invoke-Git @("tag", "--list", "v*") |
    Where-Object { $_ -match '^v(\d+)\.(\d+)\.(\d+)$' } |
    Sort-Object { [version]($_.TrimStart('v')) } |
    Select-Object -Last 1

if ($Build) {
    $Tag = "build-" + (Get-Date -Format "yyyyMMdd-HHmm")
} else {
    if (-not [string]::IsNullOrEmpty($Version)) {
        if ($Version -notmatch '^v?(\d+)\.(\d+)\.(\d+)$') { Fail "Version must look like 1.2.3 (got '$Version')." }
        $next = [version]($Version.TrimStart('v'))
    } else {
        $current = if ($latest) { [version]($latest.TrimStart('v')) } else { [version]"0.0.0" }
        $next = switch ($Bump) {
            "major" { [version]::new($current.Major + 1, 0, 0) }
            "minor" { [version]::new($current.Major, $current.Minor + 1, 0) }
            default { [version]::new($current.Major, $current.Minor, $current.Build + 1) }
        }
    }
    $Tag = "v$($next.Major).$($next.Minor).$($next.Build)"

    if ($latest -and [version]($Tag.TrimStart('v')) -le [version]($latest.TrimStart('v'))) {
        Fail "$Tag is not newer than the latest release $latest."
    }
}

# --- Safety checks -------------------------------------------------------------------
if (Invoke-Git @("tag", "--list", $Tag)) { Fail "Tag $Tag already exists." }

# Untracked files are ignored: the image is built from the pushed commit, so they can't
# end up in it. Uncommitted edits to tracked files still block, so nothing is forgotten.
$dirty = Invoke-Git @("status", "--porcelain", "--untracked-files=no")
if ($dirty) { Fail "Working tree has uncommitted changes. Commit or stash them first:`n$($dirty -join "`n")" }

if (-not $Build -and $branch -ne $ReleaseBranch) {
    Fail "Releases are tagged from '$ReleaseBranch' (you are on '$branch'). Merge first, or use -Build for a test image."
}

# The commit must be on GitHub, or the workflow would build something nobody can see.
& git rev-parse --verify --quiet "origin/$branch" *> $null
if ($LASTEXITCODE -ne 0) { Fail "Branch '$branch' is not on origin. Push it first: git push -u origin $branch" }
$ahead = [int](Invoke-Git @("rev-list", "--count", "origin/$branch..HEAD")).Trim()
$behind = [int](Invoke-Git @("rev-list", "--count", "HEAD..origin/$branch")).Trim()
if ($ahead -gt 0) { Fail "You have $ahead commit(s) not pushed to origin/$branch. Push first: git push" }
if ($behind -gt 0) { Fail "origin/$branch has $behind newer commit(s). Pull first: git pull" }

$commit = (Invoke-Git @("rev-parse", "--short", "HEAD")).Trim()
$subject = (Invoke-Git @("log", "-1", "--format=%s")).Trim()

# --- Confirm ---------------------------------------------------------------------------
Write-Host ""
Write-Host "  Tag:      $Tag" -ForegroundColor Green
Write-Host "  Previous: $(if ($latest) { $latest } else { '(none)' })" -ForegroundColor Green
Write-Host "  Branch:   $branch @ $commit  $subject" -ForegroundColor Green
if ($Build) {
    Write-Host "  Image:    ghcr.io/$Repo`:$Tag  (+ :$commit)" -ForegroundColor Green
} else {
    Write-Host "  Image:    ghcr.io/$Repo`:$($Tag.TrimStart('v')), :$($next.Major).$($next.Minor), :latest  (+ :$commit)" -ForegroundColor Green
    if ($latest) {
        $count = (Invoke-Git @("rev-list", "--count", "$latest..HEAD")).Trim()
        Write-Host "  Changes:  $count commit(s) since $latest" -ForegroundColor Green
    }
}
Write-Host ""

if (-not $Yes) {
    $confirm = Read-Host "Create and push $Tag? [y/N]"
    if ($confirm -notmatch '^[Yy]') { Write-Host "Aborted." -ForegroundColor Yellow; exit 0 }
}

# --- Tag and push ------------------------------------------------------------------------
if ([string]::IsNullOrEmpty($Message)) { $Message = if ($Build) { "Test build $Tag" } else { "Release $Tag" } }

Write-Host "Creating tag $Tag..." -ForegroundColor Cyan
Invoke-Git @("tag", "-a", $Tag, "-m", $Message) | Out-Null

Write-Host "Pushing $Tag to origin..." -ForegroundColor Cyan
& git push origin $Tag
if ($LASTEXITCODE -ne 0) {
    git tag -d $Tag | Out-Null
    Fail "Push failed; the local tag was removed so you can retry."
}

Write-Host ""
Write-Host "✓ $Tag pushed. GitHub Actions runs the tests, then builds and pushes the image." -ForegroundColor Green
Write-Host "  Runs:  https://github.com/$Repo/actions/workflows/build-and-push.yml"
Write-Host "  Image: https://github.com/$Repo/pkgs/container/enjaz"

if ($Watch) {
    if (-not (Get-Command gh -ErrorAction SilentlyContinue)) {
        Write-Host "  (-Watch needs the GitHub CLI 'gh'.)" -ForegroundColor Yellow
        exit 0
    }
    Write-Host "Waiting for the workflow run to start..." -ForegroundColor Cyan
    $runId = $null
    for ($i = 0; $i -lt 20 -and -not $runId; $i++) {
        Start-Sleep -Seconds 3
        $runId = gh run list --repo $Repo --workflow build-and-push.yml --branch $Tag --limit 1 --json databaseId --jq ".[0].databaseId" 2>$null
    }
    if ($runId) {
        gh run watch $runId --repo $Repo --exit-status
        exit $LASTEXITCODE
    }
    Write-Host "  Could not find the run yet; open the Runs link above." -ForegroundColor Yellow
}
