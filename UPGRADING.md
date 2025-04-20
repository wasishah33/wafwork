# Upgrade Guide

This document provides instructions for upgrading between major versions of the WAFWork framework.

## Upgrading to 1.0 from Beta

There are no breaking changes when upgrading from beta versions to 1.0.

## General Upgrade Process

1. **Backup Your Project**
   - Always create a backup of your entire project before upgrading

2. **Update Dependencies**
   - Update WAFWork in your composer.json file to the desired version
   ```bash
   composer require wafwork/wafwork:^x.y.z
   ```

3. **Clear Cache**
   - Clear any application cache after upgrading
   ```bash
   rm -rf storage/cache/*
   ```

4. **Test Your Application**
   - Run your test suite to ensure everything works as expected
   ```bash
   vendor/bin/phpunit
   ```

5. **Review Deprecation Notices**
   - Address any deprecation notices in your application code 