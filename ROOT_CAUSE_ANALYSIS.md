# ROOT CAUSE ANALYSIS - Grey Screen Issue

## Problem: All 10 ferries were assigned to branch 1 (DABHOL)

### Solution Applied:
Redistributed ferries across 10 branches - one ferry per branch.

### Deploy Command:
```bash
cd /var/www/unfurling.ninja
git pull origin master
php artisan db:seed --class=FerryBoatsTableSeeder
```

This fixes the grey screen issue.
