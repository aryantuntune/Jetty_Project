#!/bin/bash
################################################################################
# DATABASE RESTORE SCRIPT
# Restore PostgreSQL database from backup
#
# Usage: ./scripts/restore-database.sh [backup_file]
# Example: ./scripts/restore-database.sh backups/jetty_backup_20260212_020000.sql.gz
################################################################################

# Configuration
APP_DIR="/var/www/carferry.online"

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

# Check if backup file is provided
if [ -z "$1" ]; then
    echo "Error: No backup file specified"
    echo "Usage: $0 <backup_file>"
    echo ""
    echo "Available backups:"
    ls -lh "$APP_DIR/backups/"jetty_backup_*.sql.gz 2>/dev/null | tail -10
    exit 1
fi

BACKUP_FILE="$1"

# Check if backup file exists
if [ ! -f "$BACKUP_FILE" ]; then
    echo "Error: Backup file not found: $BACKUP_FILE"
    exit 1
fi

echo "=========================================="
echo "DATABASE RESTORE SCRIPT"
echo "=========================================="
echo "⚠️  WARNING: This will REPLACE the current database!"
echo ""
echo "Database: $DB_NAME"
echo "Backup file: $BACKUP_FILE"
echo ""
read -p "Are you sure you want to continue? (yes/no): " CONFIRM

if [ "$CONFIRM" != "yes" ]; then
    echo "Restore cancelled."
    exit 0
fi

echo ""
echo "Starting restore..."

# Decompress if needed
if [[ "$BACKUP_FILE" == *.gz ]]; then
    echo "Decompressing backup..."
    TEMP_FILE=$(mktemp)
    gunzip -c "$BACKUP_FILE" > "$TEMP_FILE"
    SQL_FILE="$TEMP_FILE"
else
    SQL_FILE="$BACKUP_FILE"
fi

# Drop existing connections
echo "Closing existing database connections..."
if [ -n "$DB_PASSWORD" ]; then
    PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '$DB_NAME' AND pid <> pg_backend_pid();"
else
    psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d postgres -c "SELECT pg_terminate_backend(pid) FROM pg_stat_activity WHERE datname = '$DB_NAME' AND pid <> pg_backend_pid();"
fi

# Restore database
echo "Restoring database..."
if [ -n "$DB_PASSWORD" ]; then
    PGPASSWORD="$DB_PASSWORD" psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" < "$SQL_FILE"
else
    psql -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USER" -d "$DB_NAME" < "$SQL_FILE"
fi

# Check if restore was successful
if [ $? -eq 0 ]; then
    echo "✓ Database restored successfully"

    # Clean up temp file
    if [[ "$BACKUP_FILE" == *.gz ]]; then
        rm -f "$TEMP_FILE"
    fi

    echo ""
    echo "=========================================="
    echo "RESTORE COMPLETED SUCCESSFULLY"
    echo "=========================================="
    exit 0
else
    echo "✗ Restore failed"

    # Clean up temp file
    if [[ "$BACKUP_FILE" == *.gz ]]; then
        rm -f "$TEMP_FILE"
    fi

    exit 1
fi
