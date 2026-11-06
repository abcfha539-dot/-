/* عدّل الروابط التالية فقط. لا تضع أسرار OAuth أو Bot Token هنا. */
window.NOVA_LINKS = {
  mainDiscord: "https://discord.gg/EE9VG6qrme",
  gangsDiscord: "https://discord.gg/8EdJvEPVp7",
  factionsDiscord: "https://discord.gg/gRaaMrVpmy",
  gameServer: "mtasa://YOUR_SERVER_IP:22003",
  store: "",
  youtube: "",
  discordLogin: "" // رابط OAuth لا يعمل وحده دون Backend callback
};

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll("[data-nova-link]").forEach(function (el) {
    const key = el.getAttribute("data-nova-link");
    const value = (window.NOVA_LINKS && window.NOVA_LINKS[key]) || "";
    const isPlaceholder = !value || value.includes("PUT_") || value.includes("YOUR_SERVER_IP");
    if (!isPlaceholder) { el.href = value; if (el.tagName === "A") el.target = "_blank"; }
    else {
      el.href = "#";
      el.addEventListener("click", function (ev) { ev.preventDefault(); alert("الرابط غير مضبوط بعد. افتح assets/js/nova-links.js وأضف الرابط الصحيح."); });
    }
  });
});
