const TOGGLE_ROOT_SELECTOR = "[data-tour-id='home-menu-mobile-toggle']";
const MENU_CONTAINER_SELECTOR = ".navigation-more";

const delay = (ms = 0) =>
  new Promise((resolve) => setTimeout(resolve, Math.max(0, Number(ms) || 0)));

export function isMobileMenuLayout() {
  if (typeof window === "undefined") return false;
  try {
    return window.matchMedia("(max-width: 750px)").matches;
  } catch (err) {
    return window.innerWidth <= 750;
  }
}

export function isMobileMenuOpen() {
  if (typeof document === "undefined") return false;
  const dropdown = document.querySelector(MENU_CONTAINER_SELECTOR);
  if (!dropdown) return false;
  const style =
    typeof window !== "undefined" && window.getComputedStyle
      ? window.getComputedStyle(dropdown)
      : null;
  if (style && (style.display === "none" || style.visibility === "hidden")) {
    return false;
  }
  const rect = dropdown.getBoundingClientRect?.();
  if (rect && rect.width <= 0 && rect.height <= 0) return false;
  return true;
}

function findToggleCandidate(wantOpen) {
  if (typeof document === "undefined") return null;
  const preferredSelectors = wantOpen
    ? [
        `${TOGGLE_ROOT_SELECTOR} img[src*='menuClosed']`,
        `${TOGGLE_ROOT_SELECTOR} [data-state='closed']`,
        `${TOGGLE_ROOT_SELECTOR} svg[data-state='closed']`,
        TOGGLE_ROOT_SELECTOR,
      ]
    : [
        `${TOGGLE_ROOT_SELECTOR} img[src*='menuOpen']`,
        `${TOGGLE_ROOT_SELECTOR} [data-state='open']`,
        `${TOGGLE_ROOT_SELECTOR} svg[data-state='open']`,
        TOGGLE_ROOT_SELECTOR,
      ];
  const fallbacks = [
    ".mobile-menu-toggle",
    ".menu-toggle",
    ".navigation-toggle",
    "button[aria-label*='menu' i]",
    "button[aria-controls*='menu' i]",
  ];
  const selectors = preferredSelectors.concat(fallbacks);
  for (const sel of selectors) {
    if (!sel) continue;
    const raw = document.querySelector(sel);
    if (!raw) continue;
    const clickable = raw.closest?.("button, .icon, [role='button']") || raw;
    if (clickable instanceof Element) return clickable;
  }
  return null;
}

function dispatchToggleClick(target) {
  if (!(target instanceof Element)) return false;
  const event = new MouseEvent("click", {
    bubbles: true,
    cancelable: true,
    view: typeof window !== "undefined" ? window : undefined,
  });
  target.dispatchEvent(event);
  return true;
}

export async function ensureMobileMenuState(
  wantOpen,
  { waitMs = 160, retries = 2 } = {}
) {
  if (typeof document === "undefined" || typeof window === "undefined") {
    return false;
  }
  if (!isMobileMenuLayout()) return false;

  const desired = !!wantOpen;
  const initialState = isMobileMenuOpen();
  if (desired === initialState) return false;

  const attempts = Math.max(0, Number(retries) || 0);

  for (let attempt = 0; attempt <= attempts; attempt += 1) {
    const toggle = findToggleCandidate(desired);
    if (!toggle) break;
    dispatchToggleClick(toggle);
    if (waitMs > 0) await delay(waitMs);
    if (isMobileMenuOpen() === desired) {
      return initialState !== desired;
    }
  }

  return initialState !== desired && isMobileMenuOpen() === desired;
}
