/**
 * Sidebar menu spread: flatten group items into a single-level menu.
 * Controlled by Template Customizer → Menu (Navigation) → Trải dài menu.
 */
'use strict';

window.AdminMenuSpread = (function () {
  var originalHtml = null;

  function storageKey() {
    return 'templateCustomizer-' + window.templateName + '--MenuSpread';
  }

  function isEnabled() {
    try {
      var stored = localStorage.getItem(storageKey());
      if (stored === null || stored === '') {
        return true;
      }
      return stored === 'true';
    } catch (e) {
      return true;
    }
  }

  function captureOriginal(menuInner) {
    if (originalHtml === null) {
      originalHtml = menuInner.innerHTML;
    }
  }

  function ensureMenuLinkIcon(menuLink, iconClass) {
    if (!menuLink || menuLink.querySelector('.menu-icon')) {
      return;
    }

    var icon = document.createElement('i');
    icon.className = 'menu-icon icon-base ' + (iconClass || 'ti tabler-circle');
    menuLink.insertBefore(icon, menuLink.firstChild);
  }

  function flattenMenuInner(menuInner) {
    captureOriginal(menuInner);
    menuInner.innerHTML = originalHtml;

    var items = Array.from(menuInner.children).filter(function (el) {
      return el.classList && el.classList.contains('menu-item');
    });

    items.forEach(function (li) {
      var sub = li.querySelector(':scope > ul.menu-sub');
      if (!sub) {
        return;
      }

      var parentIcon = li.querySelector(':scope > a.menu-link .menu-icon');

      var children = Array.from(sub.children).filter(function (el) {
        return el.classList && el.classList.contains('menu-item');
      });

      children.forEach(function (child) {
        var childLink = child.querySelector(':scope > a.menu-link');
        if (childLink && !childLink.querySelector('.menu-icon')) {
          if (parentIcon) {
            childLink.insertBefore(parentIcon.cloneNode(true), childLink.firstChild);
          } else {
            ensureMenuLinkIcon(childLink, 'ti tabler-circle');
          }
        }
        menuInner.insertBefore(child, li);
      });

      li.remove();
    });
  }

  function restoreMenuInner(menuInner) {
    captureOriginal(menuInner);
    if (originalHtml !== null) {
      menuInner.innerHTML = originalHtml;
    }
  }

  function reinitVerticalMenu() {
    if (!window.Helpers || typeof Menu === 'undefined') {
      return;
    }

    var layoutMenu = document.getElementById('layout-menu');
    if (!layoutMenu || layoutMenu.classList.contains('menu-horizontal')) {
      return;
    }

    if (window.Helpers.mainMenu) {
      window.Helpers.mainMenu.destroy();
    }

    window.Helpers.mainMenu = new Menu(layoutMenu, {
      orientation: 'vertical',
      closeChildren: false
    });

    window.Helpers.scrollToActive(false);
  }

  function applyEnabled(enabled) {
    var menuInner = document.querySelector('#layout-menu .menu-inner');
    if (!menuInner) {
      return;
    }

    document.documentElement.classList.toggle('layout-menu-spread', enabled);

    if (enabled) {
      flattenMenuInner(menuInner);
    } else {
      restoreMenuInner(menuInner);
    }
  }

  function applyBeforeMenuInit() {
    applyEnabled(isEnabled());
  }

  function setEnabled(enabled, reinitMenu) {
    try {
      localStorage.setItem(storageKey(), String(enabled));
    } catch (e) {}

    applyEnabled(enabled);

    if (reinitMenu) {
      reinitVerticalMenu();
    }
  }

  return {
    isEnabled: isEnabled,
    applyBeforeMenuInit: applyBeforeMenuInit,
    setEnabled: setEnabled
  };
})();
