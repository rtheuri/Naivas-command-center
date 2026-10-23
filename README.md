# 🛒 Naivas Marketing Command Center
> **Bespoke Multi-Department Retail Operations Prototype**  
> *Built for Naivas Supermarket Kenya • Reviewed Live by the Judging Panel*

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-336791?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Render Ready](https://img.shields.io/badge/Render-Deployment_Ready-46E3B7?style=for-the-badge&logo=render&logoColor=black)](https://render.com)

---

## 🌟 Executive Summary & Concept

The **Naivas Marketing Command Center** is a high-velocity enterprise web application tailored to Kenya’s leading supermarket chain, **Naivas Supermarket** (100+ branches nationwide). 

Retail marketing at Naivas scale involves tight cross-functional coordination. This prototype bridges the gap between creative ideation, high-budget digital media execution, and on-the-ground physical retail merchandising across branches.

### The Core Sequential Interrelation:
```
┌─────────────────────────┐
│ 1. Campaign Planning    │  CMO initiates strategy, budget caps, and timeline
└────────────┬────────────┘
             │
             ▼
┌─────────────────────────┐
│ 2. IMC Creative Sign-Off│  IMC Lead approves POSM banners & digital graphics
└────────────┬────────────┘  ➔ Automatically sets campaign status to "Ready for Launch"
             │
             ▼
┌─────────────────────────┐
│ 3. DPM Media Deployment │  DPM Media Buyer deploys ad channels (strictly gated by IMC sign-off)
└────────────┬────────────┘  ➔ Real-time pacing tracker & fund reallocation
             │
             ▼
┌─────────────────────────┐
│ 4. OME Store Execution  │  Store Managers at branches receive task checklists on mobile
└─────────────────────────┘  ➔ 1-Tap photo proof upload syncs live to the Executive Feed
```

---

## ⚖️ Frictionless Login for Judges ("Judge Portal")

Judges are spared standard email/password registration. Landing on `/` presents four prominent persona cards with **1-click instant login**:

| Persona Role | Seeded User | Focus & Modules | Instant Route |
| :--- | :--- | :--- | :--- |
| **IMC Lead** | **Amina Mwangi** | Creative Pipeline, Briefs, Asset Approvals | [`/login/as/imc_lead`](http://localhost:8000/login/as/imc_lead) |
| **DPM Media Buyer** | **Kevin Omondi** | Digital Media Pacing, Gated Ads, Budget Reallocation | [`/login/as/dpm_buyer`](http://localhost:8000/login/as/dpm_buyer) |
| **OME Store Manager** | **Faith Wanjiku** | Mobile Store Checklist, Photographic Proof Upload | [`/login/as/ome_manager`](http://localhost:8000/login/as/ome_manager) |
| **Marketing Executive** | **Peter Mumo (CMO)** | Master KPI Dashboard, Burn Rate Charts, Live Audit Feed | [`/login/as/marketing_exec`](http://localhost:8000/login/as/marketing_exec) |

> 💡 **Judge Role Switcher:** A persistent dropdown in the top navbar allows judges to switch personas instantly anywhere in the app without logging out!

---

## 🎯 Suggested Live Demonstration Script for Judges

1. **Step 1: Test IMC Creative Sign-Off (Module 1)**
   - Login as **IMC Lead** (`Amina Mwangi`).
   - Navigate to `/imc` to view the 3-column Kanban Board (*Draft*, *Pending Review*, *Approved*).
   - In the **Pending Review** column, find the asset: **"Farm Direct Crate Skirts & Overhead Decals"** (*Campaign: Fresh Harvest Week*).
   - Click **"Approve Asset"**.
   - **System Reaction:** Asset is marked `approved`, and campaign status immediately advances to **"Ready for Launch"**, unlocking actions in DPM and OME!

2. **Step 2: Test DPM Gated Deployment & Reallocation (Module 2)**
   - Click the top navbar switcher and select **DPM Media Buyer** (`Kevin Omondi`).
   - Notice that *Fresh Harvest Week* now has an active, glowing **"Deploy Ads"** button (previously locked with a disabled warning).
   - Click **"Deploy Ads"** to launch digital channels.
   - Click **"Reallocate Budget"** to simulate moving KES 250,000 from SMS broadcast to high-converting TikTok reels.

3. **Step 3: Test In-Store Mobile Proof (Module 3)**
   - Switch to **OME Store Manager** (`Faith Wanjiku` at *Naivas Mega City*).
   - Optional: Click **"Smartphone Preview Frame"** to see the mobile viewport.
   - In the store checklist, find **"Install Gondola End Banner in Fresh Produce Aisle"**.
   - Click **"Upload Proof of Execution"**, select a simulated supermarket photo preset, and submit.
   - **System Reaction:** Task updates to **"Executed - Pending Verification"**.

4. **Step 4: Verify Global Executive Hub (Centralized Command)**
   - Switch to **Marketing Executive** (`Peter Mumo - CMO`).
   - Observe the live **Activity Feed** showing all recent cross-department actions in real time.
   - Inspect the **Dual-Bar Budget Chart** and **Channel Distribution Donut Chart** (powered by Chart.js).
   - Observe the Store Rollout Completion Rate reflect the newly executed task.

---

## 🏗️ Brand Identity & Aesthetics

- **Primary Brand Green:** `#006837` (Naivas signature supermarket green)
- **Secondary Brand Orange:** `#F7941D` (Naivas signature retail accent orange)
- **UI Framework:** Tailwind CSS v4, Inter typography, clean retail badges, status pills, and responsive modals.
- **Frontend Interactivity:** Alpine.js for instantaneous UI state changes and Chart.js for data visualization.

---

## 📊 Pre-Seeded Campaigns (Mock Data)

The system boots pre-loaded with **5 active retail campaigns** and **KES 22.3 Million** in marketing budget allocations:

1. **Weekend Produce Promo** (`CMP-2026-001`): KES 3.5M budget, ready for rollout, leafy greens and avocado discounts across branches.
2. **Back to School Mega Sale** (`CMP-2026-002`): KES 6.8M budget, active nationwide, stationery packs and uniform bundles.
3. **Fresh Harvest Week** (`CMP-2026-003`): KES 2.8M budget, direct Rift Valley farm produce, pending creative sign-off *(ideal judge test)*.
4. **Naivas Super Brand Month** (`CMP-2026-004`): KES 5.0M budget, private label FMCG pantry staples in planning.
5. **Customer Appreciation Days** (`CMP-2026-005`): KES 4.2M budget, 3X loyalty points festive push across checkout tills.

---

## 🚀 Deployment Instructions for Render

This repository is **100% production-ready for deployment on Render**.

### Option A: 1-Click Blueprint (`render.yaml`)
1. Create a free account at [render.com](https://render.com).
2. Click **Blueprints** $\rightarrow$ **New Blueprint Instance**.
3. Connect your repository. Render will automatically read `render.yaml`, provision a managed **PostgreSQL 16** database, build the **Docker** web service, run migrations, and seed mock data!

### Option B: Docker Web Service
- Use the included production `Dockerfile`.
- Attach a PostgreSQL database and provide the `DATABASE_URL` environment variable.
- `docker-entrypoint.sh` automatically runs `php artisan migrate --force` and `php artisan db:seed --force` on startup!

---

## 💻 Local Development Setup

```bash
# 1. Clone repository
git clone <repo-url>
cd "Naivas Case Comp"

# 2. Install PHP dependencies
composer install

# 3. Install NPM dependencies & build assets
npm install
npm run build

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Run migrations & database seeds
php artisan migrate:fresh --seed

# 6. Run automated test suite
php artisan test

# 7. Start local server
php artisan serve
```

Visit [`http://localhost:8000`](http://localhost:8000) to open the Judge Portal!

---

## 🛡️ Automated Test Suite

A comprehensive test suite verifies all sequential workflow logic:

```bash
php artisan test --filter=MarketingCommandCenterTest
```

```
PASS  Tests\Feature\MarketingCommandCenterTest
✓ judge portal renders with four login personas
✓ frictionless login for each role
✓ executive dashboard displays all five campaigns and kpis
✓ imc asset approval automatically unlocks campaign to ready for launch
✓ dpm ad deployment gating
✓ dpm budget reallocation moves funds and recalculates
✓ ome store manager submits proof of execution

Tests:  7 passed (62 assertions)
```
