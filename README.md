# Purnukka Stack v1.5

Purnukka Stack is a modular SaaS engine designed for high-end accommodation management. It enables property owners to manage branding, AI-driven guest communication, and automated checkout processes across multiple properties through a centralized Hub.

## 🏗 Architecture Overview

The system follows a modular "Conductor-Module" pattern:

- **The Loader** (`mu-plugins/purnukka-stack-loader.php`): The entry point that boots the core engine.
- **The Core** (`mu-plugins/purnukka-stack/core.php`): The "brain" that reads configuration, manages dynamic module loading, and provides the REST API for Hub synchronization.
- **Modules** (`mu-plugins/purnukka-stack/modules/`): Independent features (AI, Branding, Tier Management) that can be toggled on/off via the Dashboard or Hub.
- **Config** (`purnukka-config/context.json`): A local, property-specific JSON file that stores all settings, technical credentials, and property rules.

## 🚀 Key Features

- **Centralized Management:** Sync settings from a centralized Hub via REST API.
- **AI-Driven Hosting:** Automated guest assistance using property-specific rules and Gemini AI.
- **Dynamic Branding:** Automatic injection of logos, colors, and legal company information.
- **Tier-Based Limits:** Built-in support for different subscription levels (Solo, Growth, Infinite).
- **Technical Automation:** Automated SMTP configuration and checkout logic for WooCommerce.

## 🛠 Installation & Setup

1. **Deploy the Engine:** Upload the `purnukka-stack` folder and the loader to your `wp-content/mu-plugins/` directory.
2. **Configuration:** Ensure a `purnukka-config/` directory exists in `wp-content/` with a valid `context.json`.
3. **Setup Data:** Fill in the `property_info` and `technical` (SMTP) sections in `context.json`.
4. **API Access:** Set your `Authorization: Bearer` token in the Core file to enable remote syncing.
5. **Hub Integration:** Point your configuration scripts to the `/wp-json/purnukka/v1/sync` endpoint.

## 🔒 Security

- **Data Isolation:** All customer-specific data is stored in `purnukka-config/` and is excluded from the Git repository via `.gitignore`.
- **API Protection:** Synchronization requires a valid Bearer Token.
- **Restricted Access:** Core logic is stored in `mu-plugins` to prevent accidental deactivation by site users.

## 📡 API Endpoints

### Configuration Sync
- **URL:** `POST /wp-json/purnukka/v1/sync`
- **Auth:** `Authorization: Bearer <TOKEN>`
- **Payload:** Full `context.json` object.

---
Developed by Purnukka. Designed for scalability and ease of use.