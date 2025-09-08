# Stripe Subscription Setup Guide

This guide will help you set up Stripe products and prices for the Kink Master subscription system.

## 1. Create Stripe Products

In your Stripe Dashboard, create the following products:

### Monthly Subscription
- **Name**: Kink Master Monthly
- **Description**: Full access to all features
- **Price**: £2.99/month (recurring)

### Couple Subscription  
- **Name**: Kink Master Couple
- **Description**: Full access for couples with shared features
- **Price**: £3.99/month (recurring)

### Lifetime Subscription
- **Name**: Kink Master Lifetime
- **Description**: One-time payment for lifetime access
- **Price**: £99.00 (one-time)

## 2. Configure Environment Variables

Add these to your `.env` file:

```env
# Stripe Configuration
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here

# Stripe Price IDs (get these from your Stripe Dashboard)
STRIPE_PRICE_MONTHLY=price_your_monthly_price_id
STRIPE_PRICE_COUPLE=price_your_couple_price_id
STRIPE_PRICE_LIFETIME=price_your_lifetime_price_id

# Cashier Configuration
CASHIER_CURRENCY=gbp
CASHIER_CURRENCY_LOCALE=en_GB
```

## 3. Set Up Webhooks

1. In your Stripe Dashboard, go to Webhooks
2. Add endpoint: `https://yourdomain.com/stripe/webhook`
3. Select these events:
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.trial_will_end`

## 4. Test the Integration

1. Run the tests: `php artisan test --filter=SubscriptionTest`
2. Test subscription creation in your application
3. Verify webhook events are being received

## 5. Subscription Plans

The system supports these plans:

- **Free**: Limited features, no payment required
- **Monthly**: £2.99/month with 14-day free trial
- **Couple**: £3.99/month with 14-day free trial  
- **Lifetime**: £99.00 one-time payment

## 6. Features by Plan

### Free Plan
- Limited task assignments (3 per day)
- Basic rewards and punishments
- Community access
- 1 active outcome

### Monthly Plan
- Unlimited task assignments
- Full rewards and punishments library
- Priority support
- Advanced analytics
- Custom task creation
- 5 active outcomes

### Couple Plan
- Everything in Monthly
- Shared couple dashboard
- Partner task assignments
- Couple-specific content
- Joint progress tracking
- 10 active outcomes

### Lifetime Plan
- Everything in Couple
- No recurring payments
- Lifetime updates
- Premium support
- Early access to new features
- Unlimited active outcomes

## 7. Admin Management

Use the Filament admin panel to manage subscriptions:

- View all subscriptions
- Filter by plan type and status
- Edit subscription details
- Monitor trial periods and billing

## 8. User Interface

Users can manage their subscriptions at `/app/subscription`:

- View current plan and status
- Upgrade or change plans
- Cancel subscriptions
- View billing history

## 9. Webhook Security

The webhook endpoint is protected by Stripe's signature verification. Make sure to:

1. Keep your webhook secret secure
2. Use HTTPS in production
3. Monitor webhook delivery in Stripe Dashboard

## 10. Testing

Use Stripe's test mode for development:

- Test cards: https://stripe.com/docs/testing
- Test webhooks: https://stripe.com/docs/webhooks/test
- Test payments: Use test card numbers

## Troubleshooting

### Common Issues

1. **Webhook not receiving events**: Check endpoint URL and event selection
2. **Payment failures**: Verify test card numbers and payment method setup
3. **Subscription not updating**: Check webhook secret and event handling
4. **Trial not working**: Verify trial period configuration in Stripe

### Debug Mode

Enable debug logging by adding to your `.env`:

```env
LOG_LEVEL=debug
CASHIER_LOGGER=default
```

This will log all Stripe API calls and webhook events.