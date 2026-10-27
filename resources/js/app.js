// Travel Umrah — global UI helpers (dibundel via Vite).
// Dipakai marketing/home (banner slider, testimonial) + package-detail (gateway toggle).

function initBannerSlider() {
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.banner-dot');
    if (!slides.length || slides.length < 2) return;
    let idx = 0;
    const go = (i) => {
        slides[idx]?.classList.remove('active');
        dots[idx]?.classList.remove('!bg-navy-700', '!w-8');
        dots[idx]?.setAttribute('aria-current', 'false');
        idx = (i + slides.length) % slides.length;
        slides[idx]?.classList.add('active');
        dots[idx]?.classList.add('!bg-navy-700', '!w-8');
        dots[idx]?.setAttribute('aria-current', 'true');
    };
    window.bannerGo = go;
    window.bannerNext = () => go(idx + 1);
    window.bannerPrev = () => go(idx - 1);
    let timer = setInterval(window.bannerNext, 5000);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clearInterval(timer);
        else timer = setInterval(window.bannerNext, 5000);
    });
}

function initTestimonials() {
    const items = document.querySelectorAll('.testimonial-item');
    const dots = document.querySelectorAll('.testimonial-dot');
    if (!items.length || items.length < 2) return;
    let idx = 0;
    window.testimonialGo = (i) => {
        items[idx]?.classList.add('hidden');
        dots[idx]?.classList.remove('!bg-white', '!w-8');
        idx = (i + items.length) % items.length;
        items[idx]?.classList.remove('hidden');
        dots[idx]?.classList.add('!bg-white', '!w-8');
    };
    let timer = setInterval(() => window.testimonialGo(idx + 1), 6000);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clearInterval(timer);
        else timer = setInterval(() => window.testimonialGo(idx + 1), 6000);
    });
}

function initGatewayToggle() {
    const radios = document.querySelectorAll('input[name="payment_method"]');
    const gatewayBox = document.getElementById('gateway-options') || document.getElementById('gateway_options');
    if (!radios.length || !gatewayBox) return;
    const sync = () => {
        const v = document.querySelector('input[name="payment_method"]:checked')?.value;
        const show = v === 'gateway';
        gatewayBox.style.display = show ? '' : 'none';
        gatewayBox.classList.toggle('hidden', !show);
    };
    radios.forEach((r) => r.addEventListener('change', sync));
    sync();
}

document.addEventListener('DOMContentLoaded', () => {
    initBannerSlider();
    initTestimonials();
    initGatewayToggle();
});
