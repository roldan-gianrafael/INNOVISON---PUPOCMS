# INNOVISON PUPOCMS - API SYSTEM PROCESSES GUIDE

## Overview
This guide documents all API processes for the centralized admin hub serving PUPT, Dental, SIS, and external integrations.

---

## 1. API ENDPOINTS & INTEGRATIONS

### Connected Systems
- **PUPT** - Primary clinic management system
- **Dental** - Dental clinic module
- **SIS** - Student Information System
- **[4th System]** - *(To be confirmed)*
- **PUPTAS** - Medical assessment system
- **GUISIS** - Faculty/student profiles system
- **One Portal** - Authentication/IdP

### API Types
- RESTful APIs (JSON)
- OAuth 2.0 PKCE Flow (One Portal)
- Webhook endpoints for external systems
- Internal microservice APIs

---

## 2. MONITORING & HEALTH CHECKS

### Daily Health Check Procedure
**Frequency:** Every 4 hours or after each deployment

```
☐ Check API response times
☐ Verify database connections
☐ Monitor error logs for failures
☐ Check external system connectivity (PUPT, Dental, SIS)
☐ Verify token/session validity
☐ Monitor memory and CPU usage
☐ Check queue job status
```

### Key Metrics to Monitor
- **Response Time:** Should be < 500ms for 90% of requests
- **Error Rate:** Should be < 0.1% 
- **Uptime:** Target 99.5%
- **Connection Pool:** Should not exceed 80% capacity
- **Memory Usage:** Should not exceed 85%

---

## 3. TROUBLESHOOTING - INTERMITTENT API FAILURES

### Symptom: API works a few times, then fails repeatedly

**Step 1: Identify the Pattern**
```
Questions to ask:
- After how many successful calls does it fail? (1st, 3rd, 5th, random?)
- Which external system is failing? (PUPT, Dental, SIS?)
- Does restarting the service fix it?
- Is it happening to all systems or just one?
```

**Step 2: Check Hostinger Logs**
```
Navigate to Hostinger Dashboard → Logs

Check these log files:
1. Error Logs: /var/log/error.log
2. Laravel Logs: storage/logs/laravel.log
3. PHP Errors: /var/log/php-errors.log
4. Access Logs: /var/log/access.log
```

**Step 3: Look for Common Errors**

| Error | Cause | Solution |
|-------|-------|----------|
| "Connection refused" | Database unreachable | Check database status, verify credentials |
| "Allowed memory exhausted" | Memory leak in code | Check for loops, unreleased connections |
| "Max connections exceeded" | Connection pool full | Increase pool size or close unused connections |
| "Connection timeout" | Slow database/network | Optimize queries, increase timeout value |
| "Token expired" | OAuth token invalid | Refresh token or re-authenticate |
| "FATAL error" | Uncaught exception | Check error_log for stack trace |

**Step 4: Resource Check**
```bash
# Check memory usage
free -h

# Check CPU usage
top -b -n 1

# Check database connections
mysql -u user -p -e "SHOW PROCESSLIST;"

# Check active PHP processes
ps aux | grep php
```

**Step 5: Connection Pool Analysis**
```
Issue: API fails after X calls
Likely Cause: Connection not being released

Check:
- Are database connections being closed properly?
- Is there a connection pool limit?
- Are transactions being committed/rolled back?
```

**Step 6: Queue Status**
If using Laravel Queues:
```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Monitor queue status
php artisan queue:monitor
```

---

## 4. DIAGNOSTICS CHECKLIST

When API fails, perform this checklist:

### 1. Immediate Actions
- [ ] Check Hostinger error logs
- [ ] Verify all external systems are responding (PUPT, Dental, SIS)
- [ ] Check database connection status
- [ ] Monitor memory/CPU usage
- [ ] Check if service needs restart

### 2. Log Analysis
```
Look for:
- Timestamp of first failure
- Error message or code
- Which endpoint/system affected
- Request parameters if available
```

### 3. Configuration Verification
```
Check config/services.php for:
- Database credentials correct?
- API keys valid and not expired?
- OAuth tokens refreshed?
- Rate limits configured?
- Timeout values appropriate?
```

### 4. Database Health
```
Check:
- Database uptime and status
- Table lock issues
- Slow query log
- Replication status (if applicable)
- Disk space available
```

---

## 5. COMMON ISSUES & SOLUTIONS

### Issue 1: API works 1-2 times, then fails
**Root Cause:** Connection not being released to pool

**Solution:**
```php
// WRONG - Connection not explicitly closed
$connection = DB::connection();
$data = $connection->table('items')->get();
// Missing: $connection->close();

// RIGHT - Use connection safely
$data = DB::table('items')->get();
// Laravel auto-manages connection lifecycle
```

### Issue 2: Timeout errors after multiple requests
**Root Cause:** Pool exhaustion or slow queries

**Solution:**
1. Increase connection pool: `config/database.php` - increase `'pool'` value
2. Optimize slow queries: Add database indexes
3. Implement query caching
4. Use connection pooling middleware

### Issue 3: Memory keeps growing
**Root Cause:** Memory leak, unbounded loops

**Solution:**
```php
// WRONG - Unbounded query
$items = Item::all(); // Loads all records
foreach ($items as $item) {
    // Process...
}

// RIGHT - Paginate or chunk
Item::chunk(1000, function ($items) {
    foreach ($items as $item) {
        // Process...
    }
});
```

### Issue 4: Token/Auth failures
**Root Cause:** OAuth tokens expired

**Solution:**
1. Implement token refresh mechanism
2. Set token expiration checks
3. Auto-refresh on 401 responses
4. Store refresh tokens securely

---

## 6. EXTERNAL SYSTEM INTEGRATION HEALTH

### PUPT Integration
- Endpoint: `config('services.pupt.api_url')`
- Authentication: Bearer token
- Health Check: `GET /api/health`
- Timeout: 30 seconds

### Dental Integration
- Endpoint: `config('services.dental.api_url')`
- Authentication: API key
- Health Check: `GET /api/status`
- Timeout: 30 seconds

### SIS Integration
- Endpoint: `config('services.sis.api_url')`
- Authentication: OAuth 2.0
- Health Check: `GET /api/ping`
- Timeout: 45 seconds

### One Portal (IdP)
- Endpoint: `config('services.idp.url')`
- Flow: OAuth 2.0 PKCE
- Token Refresh: Automatic
- Timeout: 20 seconds

---

## 7. MAINTENANCE SCHEDULE

### Daily (Every 4 hours)
- [ ] Check error logs
- [ ] Verify all external systems responding
- [ ] Monitor memory/CPU
- [ ] Check API response times

### Weekly
- [ ] Review slow query logs
- [ ] Clean up expired tokens
- [ ] Check disk space
- [ ] Verify backups completed

### Monthly
- [ ] Full system audit
- [ ] Update dependencies
- [ ] Review API performance metrics
- [ ] Test disaster recovery procedure

---

## 8. ESCALATION PROCEDURE

### Level 1: Investigation (You are here)
- Check logs
- Verify configuration
- Monitor resources
- Document findings

### Level 2: Action
- Optimize queries
- Increase resource limits
- Restart services
- Clear caches

### Level 3: Escalation
- Contact hosting provider (Hostinger)
- Review server capacity
- Consider infrastructure upgrade
- Engage development team for code review

---

## 9. PERFORMANCE TARGETS

| Metric | Target | Warning | Critical |
|--------|--------|---------|----------|
| API Response Time | < 200ms | > 500ms | > 2000ms |
| Error Rate | < 0.1% | > 1% | > 5% |
| Memory Usage | < 70% | > 85% | > 95% |
| CPU Usage | < 60% | > 80% | > 95% |
| DB Connections | < 70% | > 85% | Pool full |
| Queue Backlog | 0 | > 1000 | > 5000 |

---

## 10. QUICK REFERENCE COMMANDS

```bash
# View error logs
tail -f /var/log/error.log

# View Laravel logs
tail -f storage/logs/laravel.log

# Check database status
mysql -u username -p -e "SHOW STATUS WHERE variable_name IN ('Threads_connected', 'Questions', 'Slow_queries');"

# Clear Laravel cache
php artisan cache:clear
php artisan config:clear

# Restart PHP-FPM
sudo systemctl restart php-fpm

# Check HTTP status
curl -I https://your-api.com/api/health

# Monitor real-time logs
watch -n 5 'tail -20 /var/log/error.log'
```

---

## 11. CONTACTS & ESCALATION

| Issue | Contact | Response Time |
|-------|---------|----------------|
| API Down | Development Team | 15 minutes |
| External System Down | System Owner | 30 minutes |
| Database Issue | Hostinger Support | 1 hour |
| Critical Outage | CTO/Admin | Immediate |

---

## Document Version
- **Created:** 2026-06-09
- **Last Updated:** 2026-06-09
- **Next Review:** 2026-07-09
- **Owner:** Development Team
