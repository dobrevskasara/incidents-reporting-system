# Reported Online Incidents - Reporting System

A Laravel application that generates periodic reports (on screen, PDF, and
Excel) for reported online incidents. The reports include the number of
reports by type, age group, platform, and period, trends over time, and
comparisons with previous periods. The report view is open to everyone;
downloading PDF/Excel requires a logged-in account.

## Features

* Periodic reports (PDF and Excel) for reported online incidents
* Grouping and filtering by type, age group, platform, and status
* Trend over time, with automatic granularity (`daily`, `weekly`, `monthly`) based on the period length
* Period presets: `week`, `month`, `quarter`, `year`, `custom`
* Comparison with a previous period — automatic (previous period of equal length) or a manually chosen comparison period
* Report sections (type/age/platform/status/trend/comparison) that can be included or excluded before generating
* Authentication (login and registration); the report view is public, downloading PDF/Excel requires login
* Demo data via a seeder (~1500 reports across 24 months, with an upward trend)

## Tech stack

* Laravel 12
* PHP 8.2+
* barryvdh/laravel-dompdf (PDF report generation)
* maatwebsite/excel (Excel report generation with multiple sheets)
* SQLite (default database; easy to change in `.env`)

## Requirements

* PHP 8.2 or newer, with the `gd` and `zip` extensions enabled
* Composer
* (Node.js/npm are not required — the reports use self-contained CSS, no Vite build)

## Installation

```
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=IncidentSeeder
```

## Environment

If you're using SQLite (default), make sure the file `database/database.sqlite`
exists and that the values in `.env` point to it:

```
DB_CONNECTION=sqlite
```

If the file doesn't exist, create it manually (Windows PowerShell):

```
New-Item database\database.sqlite -ItemType File -Force
```

## Running the project

```
php artisan serve
```

Then open `http://127.0.0.1:8000/reports`.

## Core workflow

1. The user selects a period (a preset or a custom range), filters (type/platform/age group), and the sections they want included in the report.
2. The application fetches the reported incidents for the selected period, and for the comparison period (automatic or manually chosen, if comparison is included).
3. The report is displayed immediately on screen.
4. A logged-in user can download the same report as a PDF or Excel document.

## Main routes

* `/reports` — dashboard with filters and the report on screen (publicly accessible)
* `/reports/export/pdf` — download the report as PDF (requires login)
* `/reports/export/excel` — download the report as Excel (requires login)
* `/login`, `/register` — login and registration
* `/logout` — log out (POST, requires login)

