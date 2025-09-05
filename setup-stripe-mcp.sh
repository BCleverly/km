#!/bin/bash

# Stripe MCP Server Setup Script
# This script helps set up the Stripe MCP server for this Laravel project

echo "🔧 Setting up Stripe MCP Server..."

# Check if .env file exists
if [ ! -f ".env" ]; then
    echo "📋 Creating .env file from .env.example..."
    cp .env.example .env
    echo "⚠️  Please update your Stripe credentials in the .env file"
fi

# Check if Stripe MCP package is installed
if ! npm list @stripe/mcp > /dev/null 2>&1; then
    echo "📦 Installing Stripe MCP package..."
    npm install --save-dev @stripe/mcp
else
    echo "✅ Stripe MCP package already installed"
fi

# Check if STRIPE_SECRET is set
if ! grep -q "STRIPE_SECRET=sk_" .env 2>/dev/null; then
    echo "⚠️  Please set your Stripe secret key in the .env file:"
    echo "   STRIPE_SECRET=sk_test_..."
fi

echo ""
echo "🎉 Setup complete!"
echo ""
echo "Next steps:"
echo "1. Update your Stripe credentials in .env"
echo "2. Configure Cursor to use the MCP server (see STRIPE_MCP_SETUP.md)"
echo "3. Test the setup with: npx @stripe/mcp --tools=all --api-key=YOUR_STRIPE_SECRET"
echo ""
echo "📚 For detailed instructions, see STRIPE_MCP_SETUP.md"