'use strict';
(() => {
  const form = document.getElementById('demo-form');
  if (!form) return;
  const config = JSON.parse(document.getElementById('demo-config').textContent);
  const selector = form.elements.service;
  const container = document.getElementById('extra-fields');
  const steps = Array.from(form.querySelectorAll('.form-step'));
  const indicators = Array.from(form.querySelectorAll('.step-indicators li'));
  const previous = document.getElementById('previous-step');
  const next = document.getElementById('next-step');
  const submit = document.getElementById('submit-demo');
  const progress = form.querySelector('progress');
  const answers = new Map();
  let current = 0, previousService = selector.value;
  form.noValidate = true;
  function saveAnswers() {
    const saved = {};
    container.querySelectorAll('input, select, textarea').forEach(input => {
      if (input.type === 'checkbox') { saved[input.name] ||= []; if (input.checked) saved[input.name].push(input.value); }
      else if (input.type !== 'radio' || input.checked) saved[input.name] = input.value;
    });
    answers.set(previousService, saved);
  }
  function renderFields() {
    saveAnswers(); previousService = selector.value;
    container.replaceChildren();
    const service = config.services[selector.value];
    document.getElementById('service-description').textContent = service ? service.description : 'Choose a service to see its questions.';
    if (!service) return;
    const saved = answers.get(selector.value) || {};
    service.extra_fields.forEach(field => {
      const name = `extra[${field.name}]`, id = `extra-${field.name}`;
      const choices = ['radio', 'checkbox'].includes(field.type);
      const wrapper = document.createElement(choices ? 'fieldset' : 'div');
      wrapper.className = `field${choices || field.type === 'textarea' ? ' full' : ''}`;
      const label = document.createElement(choices ? 'legend' : 'label');
      label.textContent = field.label + (field.required ? ' *' : ' (optional)');
      if (!choices) label.htmlFor = id;
      wrapper.append(label);
      if (choices) {
        wrapper.id = id;
        const list = document.createElement('div'); list.className = 'choice-list';
        (field.options || []).forEach(option => {
          const item = document.createElement('label'), input = document.createElement('input');
          input.type = field.type; input.name = name + (field.type === 'checkbox' ? '[]' : ''); input.value = option;
          input.required = field.required && field.type === 'radio';
          input.checked = field.type === 'checkbox' ? (saved[input.name] || []).includes(option) : saved[name] === option;
          item.append(input, document.createTextNode(option)); list.append(item);
        });
        if (field.required && field.type === 'checkbox') wrapper.dataset.requiredChoices = 'true';
        wrapper.append(list);
      } else {
        const input = document.createElement(field.type === 'select' ? 'select' : field.type === 'textarea' ? 'textarea' : 'input');
        if (input.tagName === 'INPUT') input.type = field.type;
        input.id = id; input.name = name; input.required = field.required;
        if (field.type === 'select') {
          const empty = document.createElement('option'); empty.value = ''; empty.textContent = 'Choose an option'; input.append(empty);
          field.options.forEach(value => { const option = document.createElement('option'); option.value = value; option.textContent = value; input.append(option); });
        }
        if (field.type === 'number') { input.min = field.min ?? 0; input.max = field.max ?? 1000000; input.step = '1'; }
        else if (field.type !== 'select') input.maxLength = 2000;
        input.value = saved[name] || ''; wrapper.append(input);
      }
      container.append(wrapper);
    });
  }
  selector.addEventListener('change', renderFields);
  function showStep(index, focus = true) {
    current = index;
    steps.forEach((step, i) => { step.hidden = i !== index; });
    indicators.forEach((indicator, i) => { if (i === index) indicator.setAttribute('aria-current', 'step'); else indicator.removeAttribute('aria-current'); });
    progress.value = index + 1; progress.setAttribute('aria-label', `Step ${index + 1} of 3`);
    previous.hidden = index === 0; next.hidden = index === steps.length - 1; submit.hidden = index !== steps.length - 1;
    if (focus) steps[index].querySelector('legend').focus();
  }
  function validateStep(index) {
    const date = form.elements.preferred_date;
    date.setCustomValidity('');
    if (date.value && config.blockWeekends) {
      const day = new Date(`${date.value}T12:00:00`).getDay();
      if (day === 0 || day === 6) date.setCustomValidity('Please choose a date from Monday to Friday.');
    }
    steps[index].querySelectorAll('[data-required-choices]').forEach(group => {
      const checkbox = group.querySelector('input');
      checkbox.setCustomValidity(group.querySelector('input:checked') ? '' : 'Choose at least one option.');
    });
    for (const control of steps[index].querySelectorAll('input,select,textarea')) {
      if (!control.checkValidity()) { showStep(index, false); control.reportValidity(); return false; }
    }
    return true;
  }
  form.elements.preferred_date.addEventListener('input', event => event.target.setCustomValidity(''));
  next.addEventListener('click', () => { if (validateStep(current)) showStep(current + 1); });
  previous.addEventListener('click', () => showStep(current - 1));
  form.addEventListener('submit', event => {
    for (let i = 0; i < steps.length; i++) { if (!validateStep(i)) { event.preventDefault(); return; } }
    submit.disabled = true; submit.textContent = 'Sending your request…';
  });
  window.addEventListener('pageshow', () => { submit.disabled = document.body.hasAttribute('data-preview'); submit.textContent = 'Send demo request →'; });
  document.querySelectorAll('#form-errors a').forEach(link => link.addEventListener('click', event => {
    const target = document.getElementById(link.hash.slice(1));
    const index = target ? steps.indexOf(target.closest('.form-step')) : -1;
    if (index < 0) return;
    event.preventDefault();
    showStep(index, false);
    target.focus();
    target.scrollIntoView({ block: 'center' });
  }));
  const firstError = form.querySelector('[aria-invalid="true"], .field-error');
  const errorStep = firstError ? steps.indexOf(firstError.closest('.form-step')) : 0;
  showStep(Math.max(0,errorStep), false);
  if (config.hasErrors) document.getElementById('form-errors')?.focus();
})();
