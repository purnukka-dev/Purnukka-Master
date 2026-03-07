# ⚡ Purnukka Stack v1.6.0 MASTER

Purnukka Stack is a robust, modular SaaS engine designed for high-end short-term rental (STR) management. It centralizes property logic, branding, and automation into a stable "Master Core" architecture.

## 🏗 Architecture Overview (v1.6.0)

The system uses a **Dependency Injection** pattern to ensure stability and prevent Fatal Errors:

- **The Loader** (`mu-plugins/purnukka-stack-loader.php`): Boots the environment and defines path constants.
- **The Master Core** (`mu-plugins/purnukka-stack/core.php`): The central "Brain". It handles module whitelisting, dependency injection, and centralized configuration loading.
- **Modular Ecosystem** (`mu-plugins/purnukka-stack/modules/`): 9 independent modules (AI, Branding, Tier Management, etc.) that receive the Core instance upon initialization.
- **Data Layer** (`wp-content/purnukka-config/context.json`): A property-specific configuration file. Git-ignored for maximum security.

## 🚀 Key Features

- **Master Control:** All modules are class-based and controlled via a central whitelist.
- **Bearer Security:** API synchronization is protected by Bearer Token authentication, managed via `context.json`.
- **Hybrid Branding:** Seamlessly injects property-specific identity (logos, colors, business IDs) into UI and PDF documents.
- **SaaS Guardrails:** Tier-based access control and location limits (Access Control & Tier Manager).

## 🛠 Installation & Setup

1. **Deploy:** Upload files to `wp-content/mu-plugins/`.
2. **Context:** Ensure `wp-content/purnukka-config/context.json` exists and is writable.
3. **API Token:** Set your `api_token` inside `context.json` to enable secure synchronization.
4. **Activation:** Modules are toggled via the `features` array in the config file or the Purnukka Admin Dashboard.

## 🔒 Security

- **Zero-Inference Protection:** Configuration is stored outside the web root's public visibility and excluded from Git.
- **Endpoint Protection:** All REST API calls require a `Authorization: Bearer <TOKEN>` header.
- **System Stability:** Uses a Conductor pattern where modules cannot crash the Core if a dependency is missing.

## 📡 API Endpoints (v1.6.0)

### Villa Synchronization
- **URL:** `POST /wp-json/purnukka/v1/sync-villa`
- **Auth:** `Authorization: Bearer <TOKEN>`
- **Payload:** Dynamic villa data (name, price, capacity, etc.).

---
**Status:** v1.6.0 MASTER - Production Ready.
Developed by Purnukka Group Oy.