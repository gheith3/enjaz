/* Prepare a real email draft; this static site has no message-delivery server. */
(() => {
  const form = document.querySelector('[data-contact-form]');
  if (!form) return;
  const fields = [...form.querySelectorAll('input:not([type=radio]):not([type=file]), textarea')];
  const status = form.querySelector('.contact-status');
  const attachment = form.querySelector('#contact-file');
  const fileName = form.querySelector('[data-file-name]');
  attachment.addEventListener('change', () => { fileName.textContent = attachment.files.length ? attachment.files[0].name : 'لم يُختر ملف'; });
  fields.forEach(field => field.addEventListener('input', () => field.removeAttribute('aria-invalid')));
  form.addEventListener('submit', event => {
    event.preventDefault();
    const invalid = fields.filter(field => {
      const valid = field.checkValidity() && (!field.required || field.value.trim().length > 0);
      field.setAttribute('aria-invalid', String(!valid));
      return !valid;
    });
    status.classList.toggle('is-error', invalid.length > 0);
    if (invalid.length) {
      status.textContent = 'يرجى التحقق من البيانات وتعبئة الاسم والبريد الإلكتروني والرسالة.';
      invalid[0].focus();
      return;
    }
    const name = form.elements.name.value.trim();
    const email = form.elements.email.value.trim();
    const phone = form.elements.phone.value.trim();
    const message = form.elements.message.value.trim();
    const topic = form.elements.topic.value;
    const attachmentNote = attachment.files.length ? `\nالملف المراد إرفاقه: ${attachment.files[0].name} (يُرفق من تطبيق البريد)` : "";
    const body = `الموضوع: ${topic}\nالاسم: ${name}\nالبريد الإلكتروني: ${email}\nرقم الهاتف: ${phone || "لم يُذكر"}${attachmentNote}\n\n${message}`;
    window.location.href = `mailto:injazat2040@gmail.com?subject=${encodeURIComponent('رسالة من موقع إنجازات — ' + topic + ' — ' + name)}&body=${encodeURIComponent(body)}`;
    status.textContent = attachment.files.length ? 'أرفق الملف المختار في تطبيق البريد، ثم أرسل الرسالة.' : 'أكمل إرسال الرسالة من تطبيق البريد.';
  });
})();
