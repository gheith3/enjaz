// Ported from design/institution.html (inline script, unchanged).
document.querySelectorAll("[data-org-more]").forEach(function(b){b.addEventListener("click",function(){var o=b.closest("[data-org-about]").classList.toggle("open");b.setAttribute("aria-expanded",String(o));b.textContent=o?"إخفاء":"اقرأ المزيد";});});
