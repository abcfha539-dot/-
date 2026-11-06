// يعرض حالة حساب Discord الحالي على جميع صفحات الموقع التي تحتوي على زر تسجيل الدخول.
(function () {
  function escapeText(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));
  }

  document.addEventListener('DOMContentLoaded', async () => {
    const buttons = document.querySelectorAll('a.nova-login-btn');
    if (!buttons.length) return;
    try {
      const response = await fetch('api/discord-me.php', {
        credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' }
      });
      if (!response.ok) return;
      const user = await response.json();
      if (!user.loggedIn) return;
      const safeName = escapeText(user.name || user.username || 'Discord');
      buttons.forEach((button) => {
        button.href = 'discord-logout.php';
        button.innerHTML = (user.avatar
          ? `<img src="${escapeText(user.avatar)}" alt="" style="width:22px;height:22px;border-radius:50%;object-fit:cover;margin-left:7px"> `
          : '<i class="fa-brands fa-discord me-2"></i>') + safeName + ' · تسجيل الخروج';
        button.title = `تم تسجيل الدخول باسم ${safeName}`;
        button.setAttribute('aria-label', `الحساب ${safeName} - تسجيل الخروج`);
      });
    } catch (error) {
      console.error('تعذر تحميل حساب Discord:', error);
    }
  });
})();
