# Moodle Configurable Reports (Librería de Satoshi Edition)

> ⚠️ **Note:** This repository is a customized fork of the official [Configurable Reports plugin by jleyva](https://github.com/jleyva/moodle-block_configurablereports). 

## 📖 Overview
This fork contains specialized modifications tailored for the **Librería de Satoshi** ecosystem. It retains all the robust reporting and SQL capabilities of the original plugin while introducing custom UI logic for Excel exports and resolving strict property requirements for modern PHP servers.

## 🚀 Key Modifications & Features

### 1. PHP 8.2+ & PHP 8.3 Compatibility Fixes
- Addressed the deprecated dynamic property creation in PHP 8.2 and newer environments.
- Core `report.class.php` was updated with the `#[AllowDynamicProperties]` attribute and explicitly defined class properties (`$config`, `$currentcourseid`). 
- This prevents the server from throwing fatal `Deprecated` errors and halts during report generation or exports.

### 2. Custom Bitcoin-Themed Excel Interface
- Completely overhauled the native Excel export script (`export/xls/export.php`).
- **Bitcoin Aesthetics:** Replaced default Moodle export styles with a rich `#F7931A` (Bitcoin Orange) header UI with bold white typography.
- **Dynamic Cell Sizing:** Intelligent and automated column width adjustments based on header contexts (optimized for 'Topic', 'Link', 'ID', and 'Presentation' columns).
- **Embedded Hyperlinks:** Refined rendering of URLs inside the exported `.xlsx` file using PHP Reflection to cleanly separate the anchor `href` from the friendly label.

## 📦 Installation
To install this customized version inside your Moodle production environment:
1. Download this repository as a `.zip` archive (ensure you download the correct branch: `MOODLE_4x_STABLE`).
2. Log into Moodle as an Administrator.
3. Navigate to **Site administration > Plugins > Install plugins**.
4. Upload the `.zip` file to install the block.
5. Finish the database upgrade process and click **Purge all caches**.

## 🔄 Syncing with Upstream
If you need to incorporate security patches or Moodle 4.x/5.0 upgrades from the original author while keeping the Satoshi styling, add the upstream repository to your Git configuration:
```bash
git remote add upstream https://github.com/jleyva/moodle-block_configurablereports.git
git fetch upstream
git merge upstream/MOODLE_4x_STABLE
