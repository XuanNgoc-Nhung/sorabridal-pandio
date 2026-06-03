/**
 * Extends Template Customizer: Menu (Navigation) → Trải dài menu.
 * Load after template-customizer.js and before config.js.
 */
'use strict';

(function () {
  if (typeof TemplateCustomizer === 'undefined') {
    return;
  }

  var MENU_SPREAD_KEY = 'MenuSpread';
  var CUSTOMIZER_REOPEN_KEY = 'CustomizerPanelOpen';
  var LABEL_KEY = 'menu_spread_label';

  function setCustomizerReopenFlag() {
    try {
      var layoutName = document.documentElement.getAttribute('data-template');
      sessionStorage.setItem(
        'templateCustomizer-' + layoutName + '--' + CUSTOMIZER_REOPEN_KEY,
        'true'
      );
    } catch (e) {}
  }

  function reopenCustomizerIfNeeded(tc) {
    try {
      var layoutName = tc._getLayoutName();
      var key = 'templateCustomizer-' + layoutName + '--' + CUSTOMIZER_REOPEN_KEY;
      if (sessionStorage.getItem(key) !== 'true' || !tc.container) {
        return;
      }
      sessionStorage.removeItem(key);
      tc.container.classList.add('template-customizer-open');
      tc.update();
      if (tc._updateInterval) {
        clearInterval(tc._updateInterval);
      }
      tc._updateInterval = setInterval(function () {
        tc.update();
      }, 500);
    } catch (e) {}
  }

  var LABELS = {
    en: 'Spread menu',
    vi: 'Trải dài menu',
    fr: 'Menu étalé',
    ar: 'توسيع القائمة',
    de: 'Menü ausklappen'
  };

  Object.keys(LABELS).forEach(function (lang) {
    if (TemplateCustomizer.LANGUAGES[lang]) {
      TemplateCustomizer.LANGUAGES[lang][LABEL_KEY] = LABELS[lang];
    }
  });

  var proto = TemplateCustomizer.prototype;

  var origLoadSettings = proto._loadSettings;
  proto._loadSettings = function () {
    origLoadSettings.call(this);
    if (typeof this.settings.defaultMenuSpread === 'undefined') {
      this.settings.defaultMenuSpread = true;
    }
    var stored = this._getSetting(MENU_SPREAD_KEY);
    this.settings.menuSpread =
      stored !== '' ? stored === 'true' : Boolean(this.settings.defaultMenuSpread);
    if (this.settings.menuSpread) {
      document.documentElement.classList.add('layout-menu-spread');
    }
  };

  var origClear = proto.clearLocalStorage;
  proto.clearLocalStorage = function () {
    var layoutName = this._getLayoutName();
    try {
      localStorage.removeItem('templateCustomizer-' + layoutName + '--' + MENU_SPREAD_KEY);
    } catch (e) {}
    origClear.call(this);
  };

  var origSetLang = proto.setLang;
  proto.setLang = function (lang, updateStorage, force) {
    origSetLang.call(this, lang, updateStorage, force);
    updateMenuSpreadLabel(this);
  };

  var origSetup = proto._setup;
  proto._setup = function (container) {
    origSetup.call(this, container);
    setupMenuSpreadControl(this);
    reopenCustomizerIfNeeded(this);
  };

  function updateMenuSpreadLabel(tc) {
    if (!tc.container) {
      return;
    }
    var el = tc.container.querySelector('.template-customizer-t-menu_spread_label');
    var t = TemplateCustomizer.LANGUAGES[tc.settings.lang] || TemplateCustomizer.LANGUAGES.en;
    if (el) {
      el.textContent = t[LABEL_KEY] || LABELS.en;
    }
  }

  function setupMenuSpreadControl(tc) {
    if (!tc.container || tc.container.querySelector('.template-customizer-menuSpread')) {
      return;
    }

    if (document.querySelector('.layout-menu-horizontal')) {
      return;
    }

    var semiDarkEl = tc.container.querySelector('.template-customizer-semiDark');
    if (!semiDarkEl) {
      return;
    }

    var wrap = document.createElement('div');
    wrap.className =
      'm-0 px-6 template-customizer-menuSpread w-100 d-flex justify-content-between align-items-center pe-12';
    wrap.innerHTML =
      '<span class="form-label template-customizer-t-menu_spread_label mb-0"></span>' +
      '<label class="switch mb-0">' +
      '<input type="checkbox" class="template-customizer-menu-spread-switch switch-input"/>' +
      '<span class="switch-toggle-slider">' +
      '<span class="switch-on"></span><span class="switch-off"></span>' +
      '</span></label>';

    semiDarkEl.insertAdjacentElement('afterend', wrap);
    updateMenuSpreadLabel(tc);

    var menuSpreadSwitch = wrap.querySelector('.template-customizer-menu-spread-switch');
    if (tc.settings.menuSpread) {
      menuSpreadSwitch.setAttribute('checked', 'checked');
    }

    var menuSpreadSwitchCb = function (e) {
      var enabled = e.target.checked;
      tc._setSetting(MENU_SPREAD_KEY, enabled);
      tc.settings.menuSpread = enabled;
      setCustomizerReopenFlag();
      window.location.reload();
    };

    menuSpreadSwitch.addEventListener('change', menuSpreadSwitchCb);
    tc._listeners.push([menuSpreadSwitch, 'change', menuSpreadSwitchCb]);
  }

})();
