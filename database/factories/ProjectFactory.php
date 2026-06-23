<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    private const NAMES = [
        'Customer Portal Redesign',
        'Mobile App v2',
        'Internal Admin Dashboard',
        'Public REST API',
        'Payment Gateway Integration',
        'Single Sign-On Rollout',
        'Reporting & Analytics Pipeline',
        'Realtime Notifications Service',
        'Email Marketing Engine',
        'CI/CD Modernization',
        'Search Service Migration',
        'Multi-tenant Billing',
        'Design System Foundation',
        'Onboarding Flow Revamp',
        'Data Warehouse Migration',
    ];

    private const DESCRIPTIONS = [
        'Rebuild the customer-facing portal on a modern Laravel + Vue stack with improved accessibility and faster page loads.',
        'Refresh the mobile application with offline support, push notifications and a new design language.',
        'Centralise all internal back-office tools into a single, role-based dashboard for the operations team.',
        'Expose a clean, versioned public REST API with OAuth2, rate limiting and full OpenAPI documentation.',
        'Integrate Stripe and PayPal, add idempotent payment retries and produce monthly settlement reports.',
        'Roll out organisation-wide SSO using SAML and OIDC, including just-in-time user provisioning.',
        'Stream events into a warehouse, build dashboards in Metabase and define core product KPIs.',
        'Move legacy long-polling notifications onto WebSockets with a queue-backed fan-out architecture.',
        'Transactional + campaign email engine with templating, A/B testing and bounce handling.',
        'Replace the existing Jenkins setup with GitHub Actions, parallelised test runs and preview environments.',
        'Migrate the search layer from MySQL LIKE queries to Meilisearch with typo tolerance and synonyms.',
        'Introduce per-tenant billing, plan management and usage metering with invoice PDF generation.',
        'Establish a shared design system: tokens, primitives, documented components and Storybook.',
        'Rework the new-user onboarding flow with a progressive checklist and contextual hints.',
        'Migrate the legacy data warehouse to BigQuery with backfilled history and parity tests.',
    ];

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $idx   = $this->faker->numberBetween(0, count(self::NAMES) - 1);

        return [
            'owner_id'    => User::factory(),
            'name'        => self::NAMES[$idx],
            'description' => self::DESCRIPTIONS[$idx],
            'start_date'  => $start,
            'deadline'    => (clone $start)->modify('+'.$this->faker->numberBetween(30, 120).' days'),
        ];
    }
}
