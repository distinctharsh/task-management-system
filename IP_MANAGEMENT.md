# IP Management System

## Overview
This system provides centralized management of allowed IP addresses for public access to certain features without authentication.

## Current Allowed IPs
The following IP addresses are currently allowed for public access:

- `10.1.64.186`
- `10.1.64.187` 
- `10.1.64.189`
- `10.20.41.88`
- `127.0.0.1` (localhost)
- `::1` (IPv6 localhost)

## Configuration Location
All allowed IPs are centrally configured in `config/app.php`:

```php
'allowed_ips' => [
    '10.1.64.186',
    '10.1.64.187', 
    '10.1.64.189',
    '10.20.41.88',
    '127.0.0.1',  // localhost
    '::1',        // IPv6 localhost
],
```

## Usage

### Server-Side (Middleware)
The `CheckIPAccess` middleware uses this configuration:
```php
$allowedIPs = config('app.allowed_ips', []);
```

### Client-Side (JavaScript)
The welcome page uses this configuration:
```javascript
window.ALLOWED_IPS = @json(config('app.allowed_ips', []));
```

### Helper Methods
Use these methods in controllers:
```php
// Check if current IP is allowed
Controller::isIpAllowed();

// Check if specific IP is allowed
Controller::isIpAllowed('192.168.1.1');

// Get all allowed IPs
Controller::getAllowedIPs();
```

## Management Commands

### List Allowed IPs
```bash
php artisan ips:manage list
```

### Add New IP
```bash
php artisan ips:manage add 192.168.1.100
```

### Remove IP
```bash
php artisan ips:manage remove 192.168.1.100
```

### Clear All IPs
```bash
php artisan ips:manage clear
```

## Features That Use IP Restriction

1. **Complaint History Page** (`/complaints/history`)
   - Server-side: CheckIPAccess middleware
   - Client-side: Track Ticket button on welcome page

2. **Public Access Logic**
   - If IP is allowed and user is not authenticated → Allow access
   - If IP is not allowed → Require authentication

## Benefits of Centralized Approach

1. **Single Source of Truth**: All IPs managed in one place
2. **Easy Maintenance**: Update once, applies everywhere
3. **Consistency**: No more mismatched IPs between files
4. **Scalability**: Easy to add/remove IPs
5. **Documentation**: Clear overview of all allowed IPs

## Security Notes

- IP restrictions are applied both server-side and client-side
- Server-side validation is the primary security measure
- Client-side validation provides better user experience
- Always validate IPs server-side for critical operations 