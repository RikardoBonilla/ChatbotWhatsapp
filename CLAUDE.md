# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a ChatBot WhatsApp project that appears to be in early development stages. The project integrates with Twilio services for WhatsApp messaging functionality.

## Project Structure

- `TwilioData/` - Contains Twilio-related configuration and recovery codes
- `.gintignore` - Git ignore rules specifically pointing to `./TwilioData`

## Security Considerations

- The `TwilioData/` directory contains sensitive Twilio credentials and recovery codes
- This directory is ignored from git tracking via `.gintignore`
- Never commit or expose Twilio API keys, auth tokens, or recovery codes

## Development Setup

### Laravel Installation
The project uses **Laravel 10** (not 12 as initially planned) due to PHP version constraints.

**Common Commands:**
```bash
# Navigate to Laravel backend
cd backend/

# Install dependencies
composer install

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start development server
php artisan serve

# Run tests
php artisan test

# Code formatting
./vendor/bin/pint
```

### Technology Stack
- **Backend**: Laravel 10 (PHP 8.1+)
- **Frontend**: Blade templates + Vite
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint

## Architecture Notes

The project is set up to use Twilio for WhatsApp integration. Key architectural decisions will need to be documented as the codebase develops, including:
- Backend framework choice
- Database integration
- Message handling patterns
- Webhook configurations for Twilio