# Local GitHub Action Runner Script for Windows
Write-Host "--- Running GitHub Action Test Workflow Locally ---" -ForegroundColor Cyan

# 1. Setup Environment (.env) 
if (!(Test-Path .env)) {
    Write-Host "--> Creating .env from .env.example..." -ForegroundColor Yellow
    Copy-Item .env.example .env
}

# 2. Setup Node & Build
Write-Host "--> Installing NPM dependencies & building assets..." -ForegroundColor Yellow
npm install
npm run build

# 3. Create Database & Lock Files
Write-Host "--> Preparing Directories & Database..." -ForegroundColor Yellow
if (!(Test-Path database)) { New-Item -ItemType Directory -Path database -Force }
if (!(Test-Path storage/framework/cache)) { New-Item -ItemType Directory -Path storage/framework/cache -Force }
if (!(Test-Path storage/framework/sessions)) { New-Item -ItemType Directory -Path storage/framework/sessions -Force }
if (!(Test-Path storage/framework/views)) { New-Item -ItemType Directory -Path storage/framework/views -Force }

if (!(Test-Path database/database.sqlite)) { 
    New-Item -ItemType File -Path database/database.sqlite -Force 
}

if (!(Test-Path storage/installed.lock)) { 
    New-Item -ItemType File -Path storage/installed.lock -Force 
}

# 4. Generate Key
php artisan key:generate --force

# 5. Execute Tests
Write-Host "--> Running PHPUnit/Pest Tests..." -ForegroundColor Green

# Environment variables from test.yml
$env:DB_CONNECTION="sqlite"
$env:DB_DATABASE="database/database.sqlite"
$env:APP_KEY="base64:uP2VbgOpxm3Qv6dFfNqWzFz/P0S+7pXpU0bLzQfWn9E="
$env:LICENSE_HUB_URL="https://license.3flo.net"
$env:LICENSE_HUB_PRODUCT_SECRET="lh_test_secret"

php artisan migrate --force
php artisan test --without-tty

Write-Host "--- Done! ---" -ForegroundColor Cyan
