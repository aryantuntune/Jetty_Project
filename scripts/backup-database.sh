#!/bin/bash
################################################################################
# DATABASE BACKUP SCRIPT
# Automated PostgreSQL backup with rotation
#
# Usage: ./scripts/backup-database.sh
# Cron: 0 2 * * * /var/www/carferry.online/scripts/backup-database.sh
################################################################################

# Configuration
APP_DIR="/var/www/carferry.online"
BACKUP_DIR="/var/www/carferry.online/backups"
DATE=$(date +\%Y\%m\%d_\%H\%M\%S)
KEEP_DAYS=30

# Load environment variables
if [ -f "$APP_DIR/.env" ]; then
    export $(cat "$APP_DIR/.env" | grep -v '^#' | xargs)
fi

# Get database credentials from .env
DB_NAME=${DB_DATABASE:-jetty_db}
DB_USER=${DB_USERNAME:-postgres}
DB_PASSWORD=${DB_PASSWORD:-}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Backup filename
BACKUP_FILE="$BACKUP_DIR/jetty_backup_$DATE.sql"
COMPRESSED_FILE="$BACKUP_FILE.gz"

echo "=========================================="
echo "DATABASE BACKUP SCRIPT"
echo "=========================================="
echo "Date: $(date)"
echo "Database: $DB_NAME"
echo "Backup file: $COMPRESSED_FILE"
echo ""

# Perform backup
echo "Creating backup..."
if [ -n "$DB_PASSWORD" ]; then
    PGPASSWORD="$DB_PASSWORD" pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" > "$BACKUP_FILE"
else
    pg_dump -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" > "$BACKUP_FILE"
fi

# Check if backup was successful
if [ $? -eq 0 ]; then
    echo "✓ Backup created successfully"

    # Compress backup
    echo "Compressing backup..."
    gzip "$BACKUP_FILE"

    if [ $? -eq 0 ]; then
        echo "✓ Backup compressed successfully"
        BACKUP_SIZE=$(du -h "$COMPRESSED_FILE" | cut -f1)
        echo "Backup size: $BACKUP_SIZE"
    else
        echo "✗ Compression failed"
        exit 1
    fi
else
    echo "✗ Backup failed"
    exit 1
fi

# Remove old backups (keep last 30 days)
echo ""
echo "Cleaning old backups (keeping last $KEEP_DAYS days)..."
find "$BACKUP_DIR" -name "jetty_backup_*.sql.gz" -type f -mtime +$KEEP_DAYS -delete

# Count remaining backups
BACKUP_COUNT=$(find "$BACKUP_DIR" -name "jetty_backup_*.sql.gz" -type f | wc -l)
echo "✓ Total backups: $BACKUP_COUNT"

echo ""
echo "=========================================="
echo "BACKUP COMPLETED SUCCESSFULLY"
echo "=========================================="
echo "Latest backup: $COMPRESSED_FILE"
echo ""

# Optional: Upload to cloud storage
# Uncomment and configure if using AWS S3, Google Cloud Storage, etc.
# aws s3 cp "$COMPRESSED_FILE" s3://your-backup-bucket/jetty-backups/
# gsutil cp "$COMPRESSED_FILE" gs://your-backup-bucket/jetty-backups/

exit 0
