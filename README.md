# Purnukka Stack v1.5

Purnukka Stack is a modular SaaS engine designed for high-end accommodation management. It enables property owners to manage branding, AI-driven guest communication, and automated checkout processes across multiple villas through a centralized Hub.

## 🏗 Architecture Overview

The system follows a modular "Conductor-Module" pattern:

- **The Loader** (`mu-plugins/purnukka-stack-loader.php`): The entry point that boots the core engine.
- **The Core** (`mu-plugins/purnukka-stack/core.php`): The "brain" that reads configuration, manages dynamic module loading, and provides the REST API for Hub synchronization.
- **Modules** (`mu-plugins/purnukka-stack/modules/`): Independent features (AI, Branding, Access Control) that can be toggled on/off via the Hub.
- **Config** (`purnukka-config/context.json`): A local, property-specific JSON file that stores all settings, limits, and rules.

## 🚀 Key Features

- **Centralized Management:** Sync settings from `hub.purnukka.com` (MainWP) via REST API.
- **AI-Driven Hosting:** Automated guest assistance using property-specific rules.
- **Dynamic Branding:** Automatic injection of logos, colors, and design systems.
- **Tier-Based Limits:** Built-in support for Solo, Growth, and Infinite subscription tiers.
- **Checkout Automation:** Tax management and branded invoice generation for WooCommerce.

## 🛠 Installation & Setup

1. **Deploy the Engine:** Upload the `purnukka-stack` folder and the loader to your `wp-content/mu-plugins/` directory.
2. **Configuration:** Ensure a `purnukka-config/` directory exists in `wp-content/` with a valid `context.json`.
3. **API Access:** Set your `Authorization: Bearer` token in the Core file to enable remote syncing.
4. **Hub Integration:** Add the property to your MainWP Hub and point your configuration scripts to the `/wp-json/purnukka/v1/sync` endpoint.

## 🔒 Security

- **Data Isolation:** All customer-specific data is stored in `purnukka-config/` and is excluded from the Git repository via `.gitignore`.
- **API Protection:** Synchronization requires a valid Bearer Token.
- **Restricted Access:** Core logic is stored in `mu-plugins` to prevent accidental deactivation by users.

## 📡 API Endpoints

### Configuration Sync
- **URL:** `POST /wp-json/purnukka/v1/sync`
- **Auth:** `Authorization: Bearer <TOKEN>`
- **Payload:** Full `context.json` object.

---
Developed by Purnukka. Designed for scalability and ease of use.