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
  var LABEL_KEY = 'menu_spread_label';

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
      this.settings.defaultMenuSpread = false;
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
    var layoutsW = tc.container && tc.container.querySelector('.template-customizer-layouts');
    if (!layoutsW || layoutsW.querySelector('.template-customizer-menuSpread')) {
      return;
    }

    if (document.querySelector('.layout-menu-horizontal')) {
      return;
    }

    var wrap = document.createElement('div');
    wrap.className =
      'm-0 px-6 pb-6 template-customizer-menuSpread w-100 d-flex justify-content-between align-items-center pe-12';
    wrap.innerHTML =
      '<span class="form-label template-customizer-t-menu_spread_label mb-0"></span>' +
      '<label class="switch mb-0">' +
      '<input type="checkbox" class="template-customizer-menu-spread-switch switch-input"/>' +
      '<span class="switch-toggle-slider">' +
      '<span class="switch-on"></span><span class="switch-off"></span>' +
      '</span></label>';

    layoutsW.appendChild(wrap);
    updateMenuSpreadLabel(tc);

    var menuSpreadSwitch = wrap.querySelector('.template-customizer-menu-spread-switch');
    if (tc.settings.menuSpread) {
      menuSpreadSwitch.setAttribute('checked', 'checked');
    }

    var menuSpreadSwitchCb = function (e) {
      var enabled = e.target.checked;
      tc._setSetting(MENU_SPREAD_KEY, enabled);
      tc.settings.menuSpread = enabled;
      document.documentElement.classList.toggle('layout-menu-spread', enabled);
      if (window.AdminMenuSpread) {
        window.AdminMenuSpread.setEnabled(enabled, true);
      }
      tc.settings.onSettingsChange.call(tc, tc.settings);
    };

    menuSpreadSwitch.addEventListener('change', menuSpreadSwitchCb);
    tc._listeners.push([menuSpreadSwitch, 'change', menuSpreadSwitchCb]);
  }

})();
