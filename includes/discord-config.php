<?php
// ضع بيانات تطبيق Discord هنا من Developer Portal.
// لا تشارك هذا الملف أو Client Secret مع أي شخص.
return [
    'client_id' => '1558241334878543892',
    'client_secret' => '0LGfG7va_Mt-zR9xtXguB18qJeZ6r0fa',
    // يجب أن يطابق Redirect URI المسجل حرفيًا في Discord Developer Portal.
    'redirect_uri' => 'http://localhost/NovaRolePlay-Restored/discord-callback.php',
    'scopes' => 'identify email',
];
