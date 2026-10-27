'use strict';
const menuButton = document.querySelector('.menu-toggle');
const navigation = document.querySelector('#primary-nav');
if (menuButton && navigation) {
  menuButton.addEventListener('click', () => {
    const open = menuButton.getAttribute('aria-expanded') !== 'true';
    menuButton.setAttribute('aria-expanded', String(open));
    menuButton.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
    navigation.classList.toggle('open', open);
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && navigation.classList.contains('open')) {
      menuButton.click(); menuButton.focus();
    }
  });
}
// Preserve the user's place and close the mobile disclosure consistently.
if (menuButton && navigation) {
  navigation.addEventListener('click', event => {
    if (event.target.closest('a') && navigation.classList.contains('open')) menuButton.click();
  });
}
const contactForm = document.getElementById('contact-form');
if (contactForm) {
  const submit = contactForm.querySelector('[type="submit"]');
  contactForm.addEventListener('submit', () => {
    submit.disabled = true;
    submit.textContent = 'Sending your enquiry…';
    contactForm.setAttribute('aria-busy', 'true');
  });
  window.addEventListener('pageshow', () => {
    submit.disabled = document.body.hasAttribute('data-preview');
    submit.textContent = 'Send enquiry →';
    contactForm.removeAttribute('aria-busy');
  });
  document.getElementById('form-errors')?.focus();
}
const consultationForm = document.getElementById('consultation-form');
if (consultationForm) {
 const submit = consultationForm.querySelector('[type="submit"]');
 consultationForm.addEventListener('submit', () => { submit.disabled = true; submit.textContent = 'Reserving your time…'; });
 window.addEventListener('pageshow', () => { submit.disabled = document.body.hasAttribute('data-preview'); submit.textContent = 'Book this consultation →'; });
}
document.getElementById('form-errors')?.focus();
const briefBuilder = document.querySelector('.brief-builder');
if (briefBuilder) {
  const updateBrief = () => {
    const selections = [...briefBuilder.querySelectorAll('select')].filter(field => field.value).map(field => field.value);
    briefBuilder.querySelector('.brief-summary').textContent = selections.length ? `Your brief: ${selections.join(' · ')}` : '';
  };
  briefBuilder.addEventListener('change', updateBrief); updateBrief();
}
document.getElementById('enquiry-success')?.focus();
if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const observer = new IntersectionObserver(entries => entries.forEach(entry => {
    if (entry.isIntersecting) { entry.target.classList.add('visible'); observer.unobserve(entry.target); }
  }), { threshold: 0.08 });
  document.documentElement.classList.add('js-reveal');
  document.querySelectorAll('.reveal').forEach(item => observer.observe(item));

}
document.querySelectorAll('.filter-button').forEach(button => button.addEventListener('click', () => {
  document.querySelectorAll('.filter-button').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
  let visible = 0;
  document.querySelectorAll('[data-project-category]').forEach(card => {
    card.hidden = button.dataset.filter !== 'all' && card.dataset.projectCategory !== button.dataset.filter;
    if (!card.hidden) visible++;
  });
  const status = document.getElementById('filter-status');
  if (status) status.textContent = `${visible} project concepts shown`;
}));
// Analytics is loaded only after explicit consent; never send names or form answers.
const analyticsConfig = document.getElementById('analytics-config');
if (analyticsConfig) {
  const { id } = JSON.parse(analyticsConfig.textContent);
  const banner = document.getElementById('cookie-banner');
  let loaded = false;
  function readConsent() { try { return localStorage.getItem('semraa_analytics'); } catch { return null; } }
  function activate() {
    if (loaded) return; loaded = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () { window.dataLayer.push(arguments); };
    window.gtag('js', new Date()); window.gtag('config', id);
    const script = document.createElement('script'); script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
    document.head.append(script);
    const marker = document.querySelector('[data-conversion]');
    if (marker) {
      const key = `conversion_${marker.dataset.conversionId}`;
      try { if (!sessionStorage.getItem(key)) { window.gtag('event', 'generate_lead', { form_type: marker.dataset.conversion }); sessionStorage.setItem(key, '1'); } } catch { /* No tracking when storage is unavailable. */ }
    }
  }
  const consent = readConsent(); banner.hidden = consent !== null;
  if (consent === 'yes') activate();
  document.querySelectorAll('[data-consent]').forEach(button => button.addEventListener('click', () => {
    try { localStorage.setItem('semraa_analytics', button.dataset.consent); } catch { /* Keep choice for this page only. */ }
    banner.hidden = true;
    if (button.dataset.consent === 'yes') activate();
    else if (loaded) location.reload();
  }));
  document.querySelectorAll('.cookie-settings').forEach(button => { button.hidden = false; button.addEventListener('click', () => { banner.hidden = false; banner.querySelector('button').focus(); }); });
  document.querySelectorAll('a').forEach(link => {
    if (link.textContent.trim() === 'Discuss Your Project') link.addEventListener('click', () => {
      if (loaded) window.gtag('event', 'project_enquiry_click', { link_text: link.textContent.trim() });
    });
  });
  document.querySelectorAll('a[href*="book-demo"]').forEach(link => link.addEventListener('click', () => {
    if (loaded) window.gtag('event', 'book_demo_click', { link_text: link.textContent.trim() });
  }));
}
