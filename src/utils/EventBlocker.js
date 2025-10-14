// Pomocná funkcia na zistenie, či je admin stránka

function isAdminPage() {
  // Prispôsob podľa tvojho routera a štruktúry ciest!
  return window.location.pathname.includes("/admin");
}

export default {
  _overlayTimeout: null,
  _boundKeydown: null,
  _boundContextMenu: null,
  _boundKeydownBlock: null,
  _boundMousedown: null,
  _boundDragstart: null,
  _boundSelectStart: null,

  isPrintingAllowed() {
    try {
      // explicit global override has priority (can be set from Vue when store is ready)
      if (typeof window.haCanPrint === "boolean") return !!window.haCanPrint;

      // try to reach Vuex store from various globals
      const store =
        window.$store || (window.__app__ && window.__app__.$store) || null;
      const user = store && store.state && store.state.user;
      const roles =
        store &&
        store.state &&
        store.state.userRoles &&
        store.state.userRoles.roles;
      const v =
        user &&
        (user.edupage_in_calendar ||
          (user.isAdmin &&
            Array.isArray(roles) &&
            roles.includes("subscription_edit")));
      return !!v; // povolené aj pre admina s rolou subscription_edit
    } catch (e) {
      return false;
    }
  },

  preventDevToolsShortcuts(event) {
    const key = event.key.toLowerCase();
    const keyCode = event.keyCode;
    const ctrlShift = event.ctrlKey && event.shiftKey;

    if (
      keyCode === 123 || // F12
      (ctrlShift && keyCode === "I".charCodeAt(0)) ||
      (ctrlShift && keyCode === "C".charCodeAt(0)) ||
      (ctrlShift && keyCode === "J".charCodeAt(0)) ||
      (event.ctrlKey && keyCode === "U".charCodeAt(0)) ||
      (event.metaKey && keyCode === "U".charCodeAt(0)) // Cmd+U (Mac)
    ) {
      event.preventDefault();
      return false;
    }

    if ((event.ctrlKey || event.metaKey) && key === "p") {
      if (!this.isPrintingAllowed()) {
        event.preventDefault();
        this.blockScreenshot();
        this.showOverlay();
        return false;
      }
      return true; // allowed to print
    }

    if (event.metaKey && event.shiftKey && (key === "4" || key === "5")) {
      if (!this.isPrintingAllowed()) {
        this.blockScreenshot();
        this.showOverlay();
        return false;
      }
      return true;
    }

    if (
      (event.ctrlKey && event.shiftKey && key === "s") ||
      (event.altKey && key === "s")
    ) {
      if (!this.isPrintingAllowed()) {
        this.blockScreenshot();
        this.showOverlay();
        return false;
      }
      return true;
    }

    if (keyCode === 44) {
      if (!this.isPrintingAllowed()) {
        this.blockScreenshot();
        this.showOverlay();
        return false;
      }
      return true;
    }
  },

  blockScreenshot() {
    if (this.isPrintingAllowed()) return;

    this.addWatermark("⚠️ Screenshot zakázaný");
    this.deformContent();
    this.hideImages();

    clearTimeout(this._overlayTimeout);
    this._overlayTimeout = setTimeout(() => {
      this.removeWatermark();
      this.restoreContent();
      this.showImages();
      this.hideOverlay();
    }, 5000);
  },

  addWatermark(text) {
    if (document.getElementById("global-watermark")) return;

    const watermark = document.createElement("div");
    watermark.id = "global-watermark";
    watermark.innerText = text.repeat(200);

    Object.assign(watermark.style, {
      position: "fixed",
      top: 0,
      left: 0,
      width: "100vw",
      height: "100vh",
      fontSize: "3rem",
      color: "#ff0000",
      opacity: "0.07",
      zIndex: "999999",
      pointerEvents: "none",
      whiteSpace: "pre-wrap",
      userSelect: "none",
      textAlign: "center",
      lineHeight: "2em",
      background: "rgba(255,255,255,0.05)",
    });

    document.body.appendChild(watermark);
  },

  removeWatermark() {
    const wm = document.getElementById("global-watermark");
    if (wm) wm.remove();
  },

  deformContent() {
    document.body.style.pointerEvents = "none";
  },

  restoreContent() {
    document.body.style.filter = "";
    document.body.style.pointerEvents = "auto";
  },

  hideImages() {
    document.querySelectorAll("img").forEach((img) => {
      img.dataset.originalSrc = img.src;
      img.src = "";
    });
  },

  showImages() {
    document.querySelectorAll("img").forEach((img) => {
      if (img.dataset.originalSrc) {
        img.src = img.dataset.originalSrc;
        delete img.dataset.originalSrc;
      }
    });
  },

  showOverlay() {
    if (typeof window.haShowPrintWarning === "function") {
      window.haShowPrintWarning();
      return;
    }
    const overlay = document.getElementById("print-warning");
    if (overlay) overlay.style.display = "flex";
  },

  hideOverlay() {
    if (typeof window.haHidePrintWarning === "function") {
      window.haHidePrintWarning();
      return;
    }
    const overlay = document.getElementById("print-warning");
    if (overlay) overlay.style.display = "none";
  },

  blockUserBadActivity() {
    // pravý klik
    this._boundContextMenu = (e) => e.preventDefault();
    document.addEventListener("contextmenu", this._boundContextMenu);

    // DevTools skratky (druhé blokovanie, kôli niektorým workaroundom)
    this._boundKeydownBlock = (e) => {
      const keyCode = e.keyCode;
      const ctrlShift = e.ctrlKey && e.shiftKey;
      if (
        keyCode === 123 ||
        (ctrlShift && keyCode === "I".charCodeAt(0)) ||
        (ctrlShift && keyCode === "C".charCodeAt(0)) ||
        (ctrlShift && keyCode === "J".charCodeAt(0)) ||
        (e.ctrlKey && keyCode === "U".charCodeAt(0)) ||
        (e.metaKey && keyCode === "U".charCodeAt(0))
      ) {
        e.preventDefault();
        return false;
      }
    };
    document.addEventListener("keydown", this._boundKeydownBlock);

    // obrázky - mousedown
    this._boundMousedown = (e) => {
      if (e.target.tagName === "IMG") e.preventDefault();
    };
    document.addEventListener("mousedown", this._boundMousedown);

    // obrázky - dragstart
    this._boundDragstart = (e) => {
      if (e.target.tagName === "IMG") e.preventDefault();
    };
    document.addEventListener("dragstart", this._boundDragstart);

    // zákaz označovania textu
    this._boundSelectStart = (e) => e.preventDefault();
    document.addEventListener("selectstart", this._boundSelectStart);
  },

  // Hlavná metóda na inicializáciu blokovania
  init() {
    if (isAdminPage()) return;
    this._boundKeydown = (event) => this.preventDevToolsShortcuts(event);
    document.addEventListener("keydown", this._boundKeydown);
    window.addEventListener("beforeprint", () => {
      if (!this.isPrintingAllowed()) {
        this.blockScreenshot();
        this.showOverlay();
      }
    });

    this.blockUserBadActivity();
  },

  destroy() {
    // Hlavný keydown
    if (this._boundKeydown) {
      document.removeEventListener("keydown", this._boundKeydown);
      this._boundKeydown = null;
    }
    // Pravý klik
    if (this._boundContextMenu) {
      document.removeEventListener("contextmenu", this._boundContextMenu);
      this._boundContextMenu = null;
    }
    // Druhé keydown blokovanie
    if (this._boundKeydownBlock) {
      document.removeEventListener("keydown", this._boundKeydownBlock);
      this._boundKeydownBlock = null;
    }
    // Mousedown na obrázky
    if (this._boundMousedown) {
      document.removeEventListener("mousedown", this._boundMousedown);
      this._boundMousedown = null;
    }
    // Dragstart na obrázky
    if (this._boundDragstart) {
      document.removeEventListener("dragstart", this._boundDragstart);
      this._boundDragstart = null;
    }
    // Selectstart
    if (this._boundSelectStart) {
      document.removeEventListener("selectstart", this._boundSelectStart);
      this._boundSelectStart = null;
    }
    // Odstráň overlay, watermark atď
    this.removeWatermark();
    this.restoreContent();
    this.showImages();
    this.hideOverlay();

    document.onkeydown = null;
    document.oncontextmenu = null;
    document.onselectstart = null;
    document.onmousedown = null;
    document.ondragstart = null;

    window.onkeydown = null;
    window.oncontextmenu = null;
    window.onselectstart = null;
    window.onmousedown = null;
    window.ondragstart = null;

    // Prevent residual anonymous listeners (not strictly necessary but helps)
    ["keydown", "contextmenu", "selectstart", "mousedown", "dragstart"].forEach(
      (evt) => {
        document.removeEventListener(evt, () => {});
        window.removeEventListener(evt, () => {});
      }
    );
  },
};
