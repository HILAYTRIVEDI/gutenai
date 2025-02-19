# GutenAI - AI-Powered Content Suggestions for Gutenberg

## Overview
**GutenAI** is a WordPress plugin that enhances the Gutenberg editor with AI-powered content suggestions. It analyzes post content and provides keyword recommendations to improve SEO, engagement, and relevance. This plugin demonstrates best practices in API integration, performance optimization, and WordPress data management.

## Features
- **AI-Powered Content Suggestions**: Analyzes post content and suggests relevant keywords using the Dandelion API.
- **Seamless Gutenberg Integration**: Provides an intuitive sidebar UI for keyword recommendations.
- **Optimized for Performance**: Utilizes debouncing and efficient state management to minimize unnecessary re-renders.
- **Secure API Communication**: Implements `wp_safe_remote_get()` for secure data fetching.
- **Scalable Architecture**: Follows WordPress coding standards with autoloading for better maintainability.

## Key Components
### 1. **AI Content Suggestion Block**
A Gutenberg block that:
- Analyzes the current post content.
- Fetches relevant keywords via the Dandelion API.
- Displays keyword suggestions dynamically in the editor sidebar.

### 2. **Singleton Plugin Architecture**
The plugin follows a singleton pattern to ensure a single instance of the core class, `GutenAI\Inc\Plugin`, improving performance and maintainability.

### 3. **Debounced Input Handling**
The `RichText` input for keywords includes a debounced change handler, reducing unnecessary API calls and improving user experience.

## Installation
### From WordPress Admin:
1. Go to **Plugins > Add New**.
2. Click **Upload Plugin** and select the `gutenai.zip` file.
3. Click **Install Now**, then **Activate**.

### Manually via FTP:
1. Upload the `gutenai` folder to `/wp-content/plugins/`.
2. Activate the plugin from **Plugins > Installed Plugins** in the WordPress admin.

### Preview



## License
This plugin is licensed under GPL-2.0-or-later.

## Author
Developed by [Your Name] as a demonstration of WordPress best practices for the Full Stack Developer position at 10up.

---

This README provides a structured overview of the plugin, making it easy for reviewers to understand its purpose, functionality, and key areas to evaluate.

