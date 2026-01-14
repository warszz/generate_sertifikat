# Deployment Checklist - Certificate Builder Extension

## Pre-Deployment (Development Environment)

### Code Review
- [ ] All PHP files follow PSR-2 coding standards
- [ ] No debug code or console.log() statements
- [ ] All comments are clear and professional
- [ ] No hardcoded credentials or sensitive data

### Security Audit
- [ ] Session validation on all endpoints
- [ ] SQL injection prevention (all PDO queries use prepared statements)
- [ ] XSS prevention (all output uses htmlspecialchars())
- [ ] CSRF tokens not needed (session-based auth sufficient)
- [ ] File upload validation (if any)
- [ ] Directory traversal protection verified

### Database
- [ ] Backup existing database
- [ ] Test migration script on test database
- [ ] Verify foreign keys and cascading deletes
- [ ] Check indexes are created properly
- [ ] Verify character set is utf8mb4

### Testing
- [ ] Create new certificate works
- [ ] Edit certificate works
- [ ] Delete certificate works (verify cascading)
- [ ] Generate PDF works with all field types
- [ ] Placeholders replace correctly
- [ ] Image upload works
- [ ] Large files handled gracefully
- [ ] Unicode text works (e.g., non-English names)
- [ ] Undo/Redo functionality works
- [ ] All endpoints tested with cURL or Postman

### Browser Testing
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile browsers

### Performance Testing
- [ ] Canvas loads quickly (<2 seconds)
- [ ] Save operation completes (<1 second)
- [ ] PDF generation completes (<5 seconds)
- [ ] No memory leaks on long editing sessions
- [ ] Can handle 50+ objects on canvas

## Deployment Steps

### 1. Backup Current System
```bash
# Backup database
mysqldump -u root -p sertifikat > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf sertifikat_backup_$(date +%Y%m%d).tar.gz /path/to/sertifikat/
```

### 2. Upload New Files
```bash
# Copy all new files to server
scp -r admin/builder.php user@server:/var/www/sertifikat/admin/
scp -r admin/builder_api.php user@server:/var/www/sertifikat/admin/
scp -r admin/custom_certificates.php user@server:/var/www/sertifikat/admin/
scp -r admin/download_cert.php user@server:/var/www/sertifikat/admin/
scp -r config/migrate_builder.php user@server:/var/www/sertifikat/config/
```

### 3. Set Permissions
```bash
# Set proper permissions
chmod 644 /var/www/sertifikat/admin/builder.php
chmod 644 /var/www/sertifikat/admin/builder_api.php
chmod 644 /var/www/sertifikat/admin/custom_certificates.php
chmod 644 /var/www/sertifikat/admin/download_cert.php
chmod 644 /var/www/sertifikat/config/migrate_builder.php

# Create uploads directory
mkdir -p /var/www/sertifikat/uploads/certificates
chmod 755 /var/www/sertifikat/uploads/certificates
```

### 4. Run Migration
```bash
# Option A: Via SSH
ssh user@server
cd /var/www/sertifikat
php config/migrate_builder.php

# Option B: Via Web Browser
# Open: https://your-domain.com/sertifikat/config/migrate_builder.php
```

### 5. Update generate.php
```bash
# This should already be done during setup
# Verify the "🎨 Custom Builder" link is present
# Check line that has custom_certificates.php link
```

### 6. Verify Installation
- [ ] Log into admin panel
- [ ] Verify "🎨 Custom Builder" button appears
- [ ] Click button and verify it loads /admin/custom_certificates.php
- [ ] Create test certificate
- [ ] Verify database tables created (check with phpMyAdmin or MySQL client)
- [ ] Generate test PDF

## Post-Deployment Checklist

### Monitoring
- [ ] Check error logs for any issues
- [ ] Monitor database size growth
- [ ] Check disk space in uploads directory
- [ ] Monitor server CPU/memory during PDF generation

### Documentation
- [ ] Update server documentation
- [ ] Add builder URLs to operations manual
- [ ] Document backup/restore procedure
- [ ] Document new endpoints in API docs

### User Communication
- [ ] Notify users about new feature
- [ ] Provide QUICK_START.md to end users
- [ ] Schedule training/demo if needed
- [ ] Set up support channel for issues

### Testing After Deployment
- [ ] Test all core workflows
- [ ] Verify with multiple user accounts
- [ ] Test with various certificate designs
- [ ] Verify original template system still works
- [ ] Check PDF quality

## Rollback Plan

If issues occur, rollback to previous version:

```bash
# 1. Stop web server
systemctl stop apache2  # or nginx, php-fpm, etc.

# 2. Restore backup
tar -xzf sertifikat_backup_YYYYMMDD.tar.gz -C /var/www/

# 3. Restore database
mysql -u root -p sertifikat < backup_YYYYMMDD.sql

# 4. Start web server
systemctl start apache2

# 5. Verify functionality
# Test generate.php and original templates
```

## Configuration Optimization (Optional)

### PHP Configuration
In `php.ini`, adjust for better performance:
```ini
; For PDF generation
max_execution_time = 300
memory_limit = 256M

; For file uploads
upload_max_filesize = 50M
post_max_size = 50M

; Session handling
session.gc_maxlifetime = 3600
```

### Web Server (Apache)
Add to `.htaccess` or virtual host config:
```apache
# Gzip compression for faster transfer
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml
    AddOutputFilterByType DEFLATE text/css text/javascript
    AddOutputFilterByType DEFLATE application/javascript application/xml
</IfModule>

# Caching headers
<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Header set Cache-Control "max-age=604800, public"
</FilesMatch>

# Prevent directory listing
Options -Indexes
```

### Database Optimization
```sql
-- Analyze tables for query optimization
ANALYZE TABLE custom_certificates;
ANALYZE TABLE custom_cert_peserta;
ANALYZE TABLE generated_custom_pdf;

-- Optimize tables (run monthly)
OPTIMIZE TABLE custom_certificates;
OPTIMIZE TABLE custom_cert_peserta;
OPTIMIZE TABLE generated_custom_pdf;
```

## Maintenance Schedule

### Daily
- [ ] Monitor error logs
- [ ] Check disk space

### Weekly
- [ ] Verify PDF generation is working
- [ ] Check database backup integrity
- [ ] Review security logs

### Monthly
- [ ] Optimize database
- [ ] Clean up old PDFs (>30 days)
- [ ] Update security patches
- [ ] Review usage statistics

### Quarterly
- [ ] Full system backup
- [ ] Security audit
- [ ] Performance review
- [ ] Capacity planning

## Support Contacts

### In Case of Issues
1. Check error logs: `/var/www/sertifikat/error.log`
2. Check PHP error log: `/var/log/php-errors.log`
3. Verify database connection
4. Check file permissions on `uploads/certificates/`
5. Verify Dompdf installation

### Emergency Contact
- Development Team: [contact info]
- Database Admin: [contact info]
- Server Admin: [contact info]

## Sign-off

### Developer Sign-off
- [ ] Code reviewed and tested
- [ ] All files uploaded correctly
- [ ] Migration script verified
- [ ] Deployment instructions documented

Name: __________________ Date: __________

### System Admin Sign-off
- [ ] Server preparation complete
- [ ] Permissions set correctly
- [ ] Migration executed successfully
- [ ] Post-deployment testing passed

Name: __________________ Date: __________

### Project Manager Sign-off
- [ ] All requirements met
- [ ] User documentation complete
- [ ] Stakeholder approval obtained
- [ ] Go-live approved

Name: __________________ Date: __________

---

## Notes Section

```
Deployment Date: _______________
Deployed By: ___________________
Server: _______________________
Database: ______________________
Any Issues: 
_________________________________
_________________________________
_________________________________
```

---

**Version:** 1.0  
**Last Updated:** January 2026  
**Maintenance Owner:** [Name]  
**Review Date:** [Date]
