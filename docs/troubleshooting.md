# Troubleshooting Guide

This guide addresses common issues you might encounter with the Media Share application and provides solutions.

## Table of Contents

- [General Issues](#general-issues)
- [Frontend Issues](#frontend-issues)
- [Backend Issues](#backend-issues)
- [Media Playback Issues](#media-playback-issues)
- [API Issues](#api-issues)
- [Database Issues](#database-issues)
- [Server Configuration Issues](#server-configuration-issues)
- [Logging and Debugging](#logging-and-debugging)
- [Common Error Codes](#common-error-codes)
- [Contacting Support](#contacting-support)

## General Issues

### Application Not Loading

**Symptoms**: Blank page, loading spinner that never completes, or error message in the browser.

**Potential Causes and Solutions**:

1. **Server Down**
   - Check if the server is running
   - Verify that Apache/PHP services are active
   - Solution: Restart the web server

2. **DNS Issues**
   - Ensure the domain is correctly pointing to the server
   - Solution: Check DNS configuration and flush DNS cache

3. **Browser Cache**
   - Solution: Clear browser cache and cookies, then reload

4. **CORS Issues**
   - Check browser console for CORS errors
   - Solution: Verify that CORS headers are correctly set in `.htaccess` and PHP files

### Login Issues

**Symptoms**: Cannot log in, credentials not accepted, or session immediately expires.

**Potential Causes and Solutions**:

1. **Session Configuration**
   - Solution: Verify PHP session settings in `php.ini` and application configuration

2. **Cookies Disabled**
   - Solution: Enable cookies in the browser

3. **Invalid Credentials**
   - Solution: Ensure using correct username ("charl" or "nade")

## Frontend Issues

### UI Not Displaying Correctly

**Symptoms**: Broken layout, missing elements, or styling issues.

**Potential Causes and Solutions**:

1. **CSS Loading Failure**
   - Check browser console for 404 errors related to CSS files
   - Solution: Rebuild the frontend with `npm run build`

2. **JavaScript Errors**
   - Check browser console for JavaScript errors
   - Solution: Fix the identified JavaScript issues

3. **Outdated Browser**
   - Solution: Update to a modern browser (Chrome, Firefox, Safari, Edge)

### Search Not Working

**Symptoms**: No results returned or search functionality unresponsive.

**Potential Causes and Solutions**:

1. **API Connectivity Issues**
   - Check network tab for failed API requests
   - Solution: Verify API endpoint URLs in `apiConfig.js`

2. **Search Service Errors**
   - Check browser console for JavaScript errors
   - Solution: Debug `SearchService.js` implementation

3. **Backend Search Issues**
   - Solution: Verify `search.php` endpoint is working correctly

## Backend Issues

### PHP Errors

**Symptoms**: White screen of death, 500 error, or PHP error message.

**Potential Causes and Solutions**:

1. **PHP Version Incompatibility**
   - Solution: Ensure server runs PHP 7.4 or higher

2. **Missing Extensions**
   - Solution: Install required PHP extensions (PDO, mysqli, GD)

3. **File Permissions**
   - Solution: Set proper permissions on PHP files (644) and directories (755)

4. **Error Reporting**
   - For debugging: Temporarily enable error reporting in `config.php`
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```

### Database Connection Issues

**Symptoms**: Database errors, "Could not connect to database" messages.

**Potential Causes and Solutions**:

1. **Incorrect Credentials**
   - Solution: Verify database credentials in `config.php`

2. **Database Server Down**
   - Solution: Ensure MySQL/MariaDB service is running

3. **Connection Limits**
   - Solution: Check for and increase database connection limits

## Media Playback Issues

### Videos Not Playing

**Symptoms**: Video doesn't start, black screen, or error message.

**Potential Causes and Solutions**:

1. **MIME Type Issues**
   - Solution: Ensure proper MIME types are set in Apache configuration

2. **Codec Support**
   - Solution: Convert videos to widely supported formats (MP4 with H.264)

3. **File Permissions**
   - Solution: Check that media files are readable by the web server

4. **Media Path Issues**
   - Solution: Verify media path configuration in `config.php`

### Missing Thumbnails

**Symptoms**: Default placeholder instead of actual thumbnails.

**Potential Causes and Solutions**:

1. **Thumbnail Generation Failure**
   - Solution: Verify GD or Imagick PHP extension is installed

2. **Path Configuration**
   - Solution: Check `THUMBNAIL_PATH` in `config.php`

3. **File Permissions**
   - Solution: Ensure web server can write to thumbnail directory

## API Issues

### API Returns 404

**Symptoms**: API endpoints not found, 404 errors in network requests.

**Potential Causes and Solutions**:

1. **Subdomain Configuration**
   - Solution: Verify Apache virtual host configuration for API subdomain

2. **Rewrite Rules**
   - Solution: Check `.htaccess` files for proper rewrite