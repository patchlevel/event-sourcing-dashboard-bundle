import './app.css';

/* --- Preferences: theme and content width ------------------------------ */

const lightQuery = window.matchMedia('(prefers-color-scheme: light)');

function applyTheme(preference) {
    const light = preference === 'light' || (preference === 'auto' && lightQuery.matches);
    document.documentElement.setAttribute('data-theme', light ? 'light' : 'dark');
}

function applyWidth(preference) {
    if (preference === 'full') {
        document.documentElement.setAttribute('data-width', 'full');
    } else {
        document.documentElement.removeAttribute('data-width');
    }
}

// The inline script in base.html.twig applies the stored values before paint; this only wires the switches.
const preferences = {
    theme: {key: 'patchlevel-dashboard-theme', values: ['light', 'dark', 'auto'], fallback: 'auto', apply: applyTheme},
    width: {key: 'patchlevel-dashboard-width', values: ['fixed', 'full'], fallback: 'fixed', apply: applyWidth},
};

function readPreference(name) {
    const {key, values, fallback} = preferences[name];

    try {
        const stored = localStorage.getItem(key);
        if (values.includes(stored)) {
            return stored;
        }
    } catch {
        // storage disabled
    }

    return fallback;
}

function syncSwitch(name, value) {
    document.querySelectorAll(`[data-pref="${name}"]`).forEach((button) => {
        button.setAttribute('aria-checked', String(button.dataset.value === value));
    });
}

document.querySelectorAll('[data-pref]').forEach((button) => {
    button.addEventListener('click', () => {
        const name = button.dataset.pref;
        const value = button.dataset.value;

        try {
            localStorage.setItem(preferences[name].key, value);
        } catch {
            // storage disabled
        }

        preferences[name].apply(value);
        syncSwitch(name, value);
    });
});

Object.keys(preferences).forEach((name) => syncSwitch(name, readPreference(name)));

lightQuery.addEventListener('change', () => {
    if (readPreference('theme') === 'auto') {
        applyTheme('auto');
    }
});

/* --- Mobile navigation -------------------------------------------------- */

const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');

function setSidebar(open) {
    if (!sidebar) {
        return;
    }

    sidebar.classList.toggle('-translate-x-full', !open);
    sidebarBackdrop?.classList.toggle('hidden', !open);
    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.setAttribute('aria-expanded', String(open));
    });

    if (open) {
        sidebar.querySelector('a, button')?.focus();
    }
}

document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        setSidebar(sidebar.classList.contains('-translate-x-full'));
    });
});

sidebarBackdrop?.addEventListener('click', () => setSidebar(false));

window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar && !sidebar.classList.contains('-translate-x-full')) {
        setSidebar(false);
    }
});

/* --- Detail dialogs ----------------------------------------------------- */

document.querySelectorAll('[data-modal]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        document.getElementById(button.dataset.modal)?.showModal();
    });
});

document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.querySelectorAll('[data-modal-close]').forEach((button) => {
        button.addEventListener('click', () => dialog.close());
    });

    // A click on the backdrop lands on the dialog element itself.
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });
});

/* --- Copy to clipboard -------------------------------------------------- */

document.querySelectorAll('[data-clipboard]').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        navigator.clipboard.writeText(button.dataset.clipboard).then(() => {
            button.setAttribute('data-copied', '');
            window.setTimeout(() => button.removeAttribute('data-copied'), 1500);
        });
    });
});

/* --- Inspection: mark the event the URL hash points at ------------------ */

if (window.location.hash) {
    const marker = document.getElementById('marker-' + window.location.hash.slice(1));
    marker?.classList.remove('invisible');
}

document.querySelectorAll('[data-add-hash]').forEach((element) => {
    element.href += window.location.hash;
});

/* --- JSON highlighting -------------------------------------------------- */

function escapeHtml(text) {
    return text.replace(/[<>&]/g, (char) => ({'<': '&lt;', '>': '&gt;', '&': '&amp;'}[char]));
}

function highlightJson(text) {
    return text
        .replace(/(("([^"]|\\")+?[^\\]")|"")(\s*.)/ig, (str, g1, g2, g3, g4) => {
            const type = g4.trim() === ':' ? 'json_key' : 'json_string';
            return "<span class='" + type + "'>" + escapeHtml(g1) + '</span>' + g4;
        })
        .replace(/-?\d+\.?\d*((E|e)[+]\d+)?/ig, "<span class='json_number'>$&</span>")
        .replace(/false|true|null/ig, "<span class='json_bool'>$&</span>");
}

document.querySelectorAll('.json').forEach((element) => {
    element.innerHTML = highlightJson(element.innerHTML);
});
