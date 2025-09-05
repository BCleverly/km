# Stripe MCP Server Setup

This project includes a Stripe Model Context Protocol (MCP) server that allows AI agents to interact with Stripe's API and access Stripe's knowledge base.

## What is MCP?

Model Context Protocol (MCP) is a standard that enables AI assistants to securely connect to data sources and tools. The Stripe MCP server provides AI agents with the ability to:

- Create and manage Stripe customers
- Process payments and subscriptions
- Manage products and prices
- Handle webhooks
- Access Stripe's knowledge base for support

## Setup Instructions

### 1. Environment Configuration

Make sure your `.env` file contains your Stripe credentials:

```bash
# Stripe Configuration
STRIPE_KEY=your-stripe-publishable-key
STRIPE_SECRET=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret

# Stripe MCP Server Configuration
# Set to 'test' for development, 'live' for production
STRIPE_MODE=test
```

### 2. Cursor Configuration

The MCP server is configured in `mcp-config.json`. To use it with Cursor:

1. Copy the configuration from `mcp-config.json` to your Cursor settings
2. Or place the file in your Cursor configuration directory
3. Restart Cursor to load the MCP server

### 3. Testing the Setup

You can test the MCP server by running:

```bash
npx @stripe/mcp --tools=all --api-key=YOUR_STRIPE_SECRET_KEY
```

## Available Tools

The Stripe MCP server provides the following tools:

- **Customer Management**: Create, update, and retrieve customer information
- **Payment Processing**: Handle one-time payments and subscriptions
- **Product Management**: Create and manage products and prices
- **Webhook Handling**: Process and verify webhook events
- **Knowledge Base**: Access Stripe's documentation and support resources

## Security Considerations

- **API Keys**: Never expose your Stripe secret keys in client-side code
- **Webhooks**: Use webhook secrets to verify event authenticity
- **Testing**: Utilize Stripe's test mode for development and testing
- **Permissions**: Ensure your Stripe account has appropriate permissions

## Development vs Production

- **Development**: Use test mode with test API keys
- **Production**: Use live mode with live API keys
- **Environment Variables**: Update `STRIPE_MODE` accordingly

## Troubleshooting

1. **MCP Server Not Loading**: Check that the `@stripe/mcp` package is installed
2. **API Key Issues**: Verify your Stripe secret key is correct and has proper permissions
3. **Environment Variables**: Ensure all required environment variables are set

## Additional Resources

- [Stripe MCP Documentation](https://docs.stripe.com/mcp)
- [Stripe API Documentation](https://stripe.com/docs/api)
- [Model Context Protocol Specification](https://modelcontextprotocol.io/)