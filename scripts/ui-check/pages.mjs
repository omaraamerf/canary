// What ui-check visits. Detail pages are found on their list pages, so the list works with
// whatever data the database holds; pages that need data the database lacks are skipped.

export const VIEWPORTS = [
    { name: 'desktop', width: 1440, height: 900, mobile: false },
    { name: 'phone', width: 390, height: 844, mobile: true },
];

export const GUEST_PAGES = [
    ['home', '/'],
    ['birds', '/birds'],
    ['bird', { from: '/birds', link: 'a.bird-card-link' }],
    ['guide', '/guide'],
    ['guide-section', { from: '/guide', link: 'a.topic-tile' }],
    ['guide-article', { from: '/guide', link: 'a.article-row-link, .article-feature a' }],
    ['community', '/community'],
    ['community-post', { from: '/community', link: 'a.post-row-link' }],
    ['favorites', '/favorites'],
    ['about', '/about'],
    ['policy', '/policy'],
    ['start-selling', '/start-selling'],
    ['login', '/login'],
    ['register', '/register'],
    ['seller-register', '/seller/register'],
    ['track-order', '/orders/track'],
    ['not-found', '/no-such-page', 404],
    ['offline', '/offline'],
];

// Signed in as the seeded admin (database/seeders/MarketplaceSeeder.php).
export const ADMIN_PAGES = [
    ['account', '/account'],
    ['account-settings', '/account/edit'],
    ['admin-dashboard', '/admin'],
    ['admin-birds', '/admin/birds'],
    ['admin-add-bird', '/admin/birds/create'],
    ['admin-orders', '/admin/orders'],
];

// Also checked in dark mode and in English, on desktop.
export const THEME_PAGES = ['home', 'bird', 'guide-article', 'login', 'admin-dashboard'];

// Values that change by themselves (counters, "3 hours ago", charts) are blanked in screenshots,
// keeping their space, so a comparison only flags changes someone made.
export const MASKED = [
    'time', '.hero-stats dd', '.profile-stats dd',
    '.fi-wi-stats-overview-stat-value', '.fi-wi-stats-overview-stat-description',
    '.fi-wi-stats-overview-stat-chart', '.fi-wi-chart canvas',
];
