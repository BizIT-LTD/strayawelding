const menuToggle = document.querySelector("[data-menu-toggle]");
const menu = document.querySelector("[data-menu]");

if (menuToggle && menu) {
  menuToggle.addEventListener("click", () => {
    const isOpen = menu.classList.toggle("open");
    menuToggle.setAttribute("aria-expanded", String(isOpen));
  });
}

const notice = document.querySelector("[data-notice]");
if (notice) {
  const params = new URLSearchParams(window.location.search);
  const status = params.get("status");

  if (status === "success") {
    notice.textContent = "Thanks. Your enquiry has been sent and Straya Mobile Welding will respond as soon as possible.";
    notice.classList.add("show", "success");
  }

  if (status === "error") {
    notice.textContent = "Sorry, your enquiry could not be sent. Please call 0416 065 680 or email info@strayawelding.com.au.";
    notice.classList.add("show", "error");
  }
}

