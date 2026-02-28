# Purnukka Stack (v0.5)
**The Ultimate Direct Booking SaaS Engine for Short-Term Rental Owners.**

Purnukka Stack is a specialized WordPress-based hosting environment designed for property owners transitioning from Airbnb and Booking.com to direct bookings. It utilizes a "Single Core, Multi-Tier" architecture to manage multiple service levels from a single codebase.

---

## 🚀 Core Components (mu-plugins)

The stack is driven by three main logic controllers located in the `mu-plugins` directory:

1.  **Core Branding (v0.4)**: 
    - Handles white-labeling of the WP Admin.
    - Injects dynamic CSS variables (Colors) based on the tier.
    - Customizes the admin footer and removes WordPress branding.

2.  **Access Control (v0.4)**: 
    - Enforces subscription limits (Solo, Growth, Infinite).
    - Automatically hides "Add New" buttons in MotoPress if the location limit is reached.
    - Prevents unauthorized property creation via direct URLs.

3.  **AI Tier Controller (v0.5)**: 
    - Connects the property rules to the AI engine (Meow Apps/MWAI).
    - Overwrites system instructions with property-specific data (Address, Capacity, Rules).
    - Adapts the AI's tone and role based on the selected tier.

---

## 🛠 Tier Configuration

All environment-specific settings are managed via a protected JSON file:
`wp-content/purnukka-config/context.json`

### Tier Structure:
- **Solo**: 1 Location (Standard AI & Support)
- **Growth**: up to 5 Locations (Enhanced AI & Reports)
- **Infinite**: Unlimited Locations (Enterprise AI & Multi-Calendar)

> **Important:** The `purnukka-config/` directory is excluded from GitHub deployments to protect local settings during core updates.

---

## 📦 Deployment Workflow

1.  **Develop**: Push changes to the `main` branch on GitHub.
2.  **Deploy**: GitHub Actions syncs the `mu-plugins` to all target environments (Master, Solo, Growth, Infinite).
3.  **Inherit**: All child sites receive the core updates immediately while keeping their unique `context.json` settings intact.

---

## 📜 Version History
- **v0.5**: Refined AI Controller with English-first logic.
- **v0.4**: Integrated Tier-based Access Control and dynamic branding.
- **v0.2**: Initial GitHub Actions deployment setup.

---
*Created and maintained by Purn