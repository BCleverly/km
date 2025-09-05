# Laravel Boost MCP Configuration

This folder contains the MCP (Model Context Protocol) configuration for Laravel Boost integration with Cursor.

## Setup Instructions

To complete the Laravel Boost setup:

1. **Install Laravel Boost** (requires PHP and Composer):
   ```bash
   composer require laravel/boost --dev
   ```

2. **Run the Laravel Boost installer**:
   ```bash
   php artisan boost:install
   ```
   - Select "Cursor" when prompted for your editor
   - The installer will automatically configure the MCP server

3. **Verify the setup**:
   - Open your project in Cursor
   - The editor should now be connected to the Laravel Boost MCP server
   - You'll have access to enhanced AI-assisted development features specific to Laravel

## MCP Configuration

The `mcp.json` file in this directory contains the MCP server configuration for Laravel Boost. This file tells Cursor how to connect to the Laravel Boost MCP server.

## Features

Once properly set up, Laravel Boost provides:
- Laravel-specific AI context and tools
- Enhanced code generation and suggestions
- Framework-aware assistance
- Best practices guidance for Laravel development

## Troubleshooting

If you encounter issues:
1. Ensure PHP and Composer are installed and accessible
2. Verify that Laravel Boost is properly installed via Composer
3. Check that the `artisan boost:mcp` command works
4. Restart Cursor after making configuration changes