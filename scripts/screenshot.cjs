const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const BASE = 'http://travelumrah.test';
const SCREEN_DIR = path.join(__dirname, '..', 'public', 'marketing', 'screens');
const ADMIN = `${BASE}/admin`;

fs.mkdirSync(SCREEN_DIR, { recursive: true });

async function shot(page, name, url) {
    console.log(`Capturing: ${name}`);
    try {
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(1500);
        await page.screenshot({ path: path.join(SCREEN_DIR, `${name}.png`), fullPage: true });
    } catch (e) {
        console.log(`  FAILED: ${name} - ${e.message.substring(0, 80)}`);
    }
}

(async () => {
    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 },
        deviceScaleFactor: 1,
    });
    const page = await context.newPage();

    // ── Marketing pages (no login needed) ──
    console.log('\n--- Marketing Pages ---');
    await shot(page, '01-home', BASE);
    await shot(page, '02-packages', `${BASE}/paket-umrah`);
    await shot(page, '03-package-detail', `${BASE}/paket-umrah/umrah-ramadhan-2026`);
    await shot(page, '04-about', `${BASE}/tentang-kami`);
    await shot(page, '05-contact', `${BASE}/kontak`);
    await shot(page, '06-docs', `${BASE}/docs`);
    await shot(page, '07-seo-best', `${BASE}/s/best-umrah-packages-2026`);
    await shot(page, '08-seo-category', `${BASE}/s/umrah-vip`);
    await shot(page, '41-blog', `${BASE}/blog`);
    await shot(page, '42-faq', `${BASE}/faq`);

    // ── Admin login ──
    console.log('\n--- Login Page ---');
    await shot(page, '09-admin-login', `${ADMIN}/login`);

    // ── Login ──
    console.log('\nLogging into admin...');
    await page.goto(`${ADMIN}/login`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(1000);

    const emailInput = page.locator('input[type="email"]').first();
    const passwordInput = page.locator('input[type="password"]').first();
    if (await emailInput.isVisible()) {
        await emailInput.fill('');
        await emailInput.pressSequentially('admin@travelumrah.com', { delay: 30 });
    }
    if (await passwordInput.isVisible()) {
        await passwordInput.fill('');
        await passwordInput.pressSequentially('password', { delay: 30 });
    }
    await page.waitForTimeout(300);
    await page.locator('button[type="submit"]').first().click();
    try { await page.waitForURL('**/admin**', { timeout: 15000 }); } catch (e) {}
    await page.waitForTimeout(3000);

    // ── Admin pages ──
    console.log('\n--- Admin Pages ---');
    const adminPages = [
        ['10-admin-dashboard', `${ADMIN}`],
        ['11-admin-packages', `${ADMIN}/packages`],
        ['12-admin-pilgrims', `${ADMIN}/pilgrims`],
        ['13-admin-bookings', `${ADMIN}/bookings`],
        ['14-admin-invoices', `${ADMIN}/invoices`],
        ['15-admin-clusters', `${ADMIN}/clusters`],
        ['16-admin-leads', `${ADMIN}/leads`],
        ['17-admin-visas', `${ADMIN}/visas`],
        ['18-admin-booking-create', `${ADMIN}/bookings/create`],
        ['19-admin-pilgrim-create', `${ADMIN}/pilgrims/create`],
        ['20-admin-branches', `${ADMIN}/branches`],
        ['21-admin-users', `${ADMIN}/users`],
        ['22-admin-hotels', `${ADMIN}/hotels`],
        ['23-admin-buses', `${ADMIN}/buses`],
        ['24-admin-equipment', `${ADMIN}/equipment`],
        ['25-admin-payments', `${ADMIN}/payments`],
        ['26-admin-expenses', `${ADMIN}/expenses`],
        ['27-admin-broadcasts', `${ADMIN}/broadcasts`],
        ['28-admin-loyalty-points', `${ADMIN}/loyalty-points`],
        ['29-admin-flights', `${ADMIN}/flights`],
        ['30-admin-insurances', `${ADMIN}/insurances`],
        ['31-admin-blog-posts', `${ADMIN}/blog-posts`],
        ['32-admin-settings', `${ADMIN}/settings`],
        ['33-admin-promo-codes', `${ADMIN}/promo-codes`],
        ['34-admin-laporan-penjualan', `${ADMIN}/laporan-penjualan`],
        ['35-admin-laporan-keuangan', `${ADMIN}/laporan-keuangan`],
        ['36-admin-laporan-operasional', `${ADMIN}/laporan-operasional`],
        ['37-admin-faqs', `${ADMIN}/faqs`],
        ['38-admin-testimonials', `${ADMIN}/testimonials`],
        ['39-admin-sliders', `${ADMIN}/sliders`],
        ['40-portal-login', `${BASE}/portal/login`],
    ];

    for (const [name, url] of adminPages) {
        await shot(page, name, url);
    }

    await browser.close();
    console.log('\nAll screenshots done! (42 total)');
})();
