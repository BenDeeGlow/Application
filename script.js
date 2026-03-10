// ─── Custom Cursor (no lag: transform instead of left/top) ───
const cursor = document.querySelector('.cursor');
if (cursor) {
    let raf;
    document.addEventListener('mousemove', e => {
        if (raf) cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
            cursor.style.transform = `translate(${e.clientX}px, ${e.clientY}px) translate(-50%, -50%)`;
        });
    });
    document.querySelectorAll('a, button, .experience-item, .academic-item, .contact-item, .download-entry')
        .forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
        });
    document.addEventListener('mouseleave', () => cursor.style.opacity = '0');
    document.addEventListener('mouseenter', () => cursor.style.opacity = '1');
}

// ─── Header Scroll Shrink ────────────────────────────────────
const header = document.querySelector('header');
if (header) {
    window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 55);
    }, { passive: true });
}

// ─── Menu Toggle ─────────────────────────────────────────────
function toggleMenu() {
    const overlay = document.getElementById('menuOverlay');
    if (!overlay) return;
    const open = overlay.classList.toggle('active');
    document.body.style.overflow = open ? 'hidden' : '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const o = document.getElementById('menuOverlay');
        if (o && o.classList.contains('active')) toggleMenu();
    }
});

// ─── Scroll Animations ───────────────────────────────────────
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

document.querySelectorAll(
    '.section-title, .section-text, .experience-item, .academic-item, .contact-item'
).forEach(el => observer.observe(el));

// ─── Page Fade In ────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    document.body.style.opacity = '0';
    requestAnimationFrame(() => {
        document.body.style.transition = 'opacity 0.45s ease';
        document.body.style.opacity = '1';
    });
});
