#!/bin/bash
# Backup current work before GitHub pull
echo "Creating backup of current work..."

# Create backup directory with timestamp
BACKUP_DIR="backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup critical files
cp *.php "$BACKUP_DIR/" 2>/dev/null || true
cp *.py "$BACKUP_DIR/" 2>/dev/null || true
cp *.bat "$BACKUP_DIR/" 2>/dev/null || true
cp *.md "$BACKUP_DIR/" 2>/dev/null || true
cp *.json "$BACKUP_DIR/" 2>/dev/null || true

# Backup modified migrations
cp -r database/migrations "$BACKUP_DIR/database/" 2>/dev/null || true

echo "Backup created in: $BACKUP_DIR"
echo "Files backed up:"
ls -la "$BACKUP_DIR/"
