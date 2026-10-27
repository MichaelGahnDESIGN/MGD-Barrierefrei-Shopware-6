// Admin-Seite "Weitere Plugins" - fertig geschrieben, kein Build nötig
// Daten kommen aus POST /api/_action/mgd-barrierefrei/catalog (Feed von theme.michael-gahn.de)
(function () {
    'use strict';

    if (typeof Shopware === 'undefined') {
        return;
    }

    // Links + Bilder nur von diesen Adressen (zweite Sperre, Server prüft auch)
    var LINK_PREFIXES = ['https://theme.michael-gahn.de/', 'https://github.com/MichaelGahnDESIGN/'];
    var IMAGE_PREFIX = 'https://theme.michael-gahn.de/uploads/catalog/';
    var STYLE_ID = 'mgd-barrierefrei-catalog-style';

    var STYLE = [
        '.mgd-cat-intro{margin:0 0 12px}',
        '.mgd-cat-note{margin:12px 0 0;font-size:13px;color:#52667a}',
        '.mgd-cat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-top:16px}',
        '.mgd-cat-item{--mgd-accent:#9aa8b6;display:flex;flex-direction:column;gap:8px;padding:16px;border:1px solid #d1d9e0;border-left:4px solid var(--mgd-accent);border-radius:6px;background:#fff}',
        '.mgd-cat-item h3{margin:0;font-size:16px}',
        '.mgd-cat-image{display:block;width:100%;height:auto;max-height:160px;object-fit:cover;border-radius:4px}',
        '.mgd-cat-head{display:flex;align-items:center;gap:10px}',
        '.mgd-cat-icon{flex:none;width:36px;height:36px;border-radius:6px;object-fit:cover}',
        '.mgd-cat-icon--fallback{display:flex;align-items:center;justify-content:center;background:#eef2f6;color:#1f2d3d;font-weight:700}',
        '.mgd-cat-badges{display:flex;flex-wrap:wrap;gap:6px;margin:0;padding:0;list-style:none}',
        '.mgd-cat-badge{padding:2px 8px;border-radius:10px;background:#eef2f6;color:#1f2d3d;font-size:12px}',
        '.mgd-cat-badge--rec{background:#1f2d3d;color:#fff}',
        '.mgd-cat-text{margin:0;flex:1}',
        '.mgd-cat-links{display:flex;flex-wrap:wrap;gap:6px 14px;margin:0}',
        '.mgd-cat-empty{padding:16px;border:1px dashed #9aa8b6;border-radius:6px}',
        // Text nur für Screenreader
        '.mgd-barrierefrei-catalog .sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}'
    ].join('');

    function addStyle() {
        if (document.getElementById(STYLE_ID)) {
            return;
        }
        var el = document.createElement('style');
        el.id = STYLE_ID;
        el.textContent = STYLE;
        document.head.appendChild(el);
    }

    function startsWithAny(url, prefixes) {
        return typeof url === 'string' && prefixes.some(function (prefix) {
            return url.indexOf(prefix) === 0;
        });
    }

    Shopware.Locale.extend('de-DE', {
        'mgd-barrierefrei-catalog': {
            menuTitle: 'MGD Weitere Plugins',
            description: 'Weitere kostenlose Plugins von Michael Gahn DESIGN',
            title: 'Weitere Plugins von Michael Gahn DESIGN',
            intro: 'Hier siehst du weitere Plugins für Shopware 6. Es wird nichts automatisch installiert. Zum Installieren lädst du das ZIP herunter und lädst es unter Erweiterungen > Meine Erweiterungen > Apps hoch.',
            refresh: 'Liste aktualisieren',
            loading: 'Liste wird geladen ...',
            unavailable: 'Die Liste ist gerade nicht erreichbar. Dein Shop läuft ganz normal weiter. Versuche es später noch einmal.',
            empty: 'Im Moment gibt es keine weiteren Plugins für Shopware 6.',
            stale: 'Achtung: Die Liste konnte nicht neu geladen werden. Du siehst den letzten gespeicherten Stand.',
            updatedAt: 'Stand der Liste:',
            privacy: 'Datenschutz: Zum Laden der Liste verbindet sich dein Shop-Server mit theme.michael-gahn.de. Es werden keine Shop-, Kunden- oder Zugangsdaten gesendet, der Server sieht aber die IP-Adresse deines Shop-Servers. Der Katalog liefert derzeit keine Bilder. Falls er später welche liefert, lädt dein Browser sie nur auf dieser Admin-Seite von theme.michael-gahn.de; dann sieht der Server deine IP-Adresse. Besucher deines Shops sind nicht betroffen.',
            recommended: 'Empfohlen',
            free: 'Kostenlos',
            paid: 'Kostenpflichtig',
            announced: 'Demnächst',
            version: 'Version',
            download: 'ZIP herunterladen',
            details: 'Mehr erfahren',
            source: 'Quellcode auf GitHub',
            newTab: '(öffnet in neuem Tab)'
        }
    });

    Shopware.Locale.extend('en-GB', {
        'mgd-barrierefrei-catalog': {
            menuTitle: 'MGD more plugins',
            description: 'More free plugins by Michael Gahn DESIGN',
            title: 'More plugins by Michael Gahn DESIGN',
            intro: 'Here you can see more plugins for Shopware 6. Nothing is installed automatically. To install one, download the ZIP and upload it under Extensions > My extensions > Apps.',
            refresh: 'Refresh list',
            loading: 'Loading list ...',
            unavailable: 'The list is not reachable right now. Your shop keeps running as usual. Please try again later.',
            empty: 'There are no more plugins for Shopware 6 at the moment.',
            stale: 'Note: The list could not be refreshed. You see the last saved state.',
            updatedAt: 'List updated:',
            privacy: 'Privacy: To load the list, your shop server connects to theme.michael-gahn.de. No shop, customer or login data is sent, but the server sees the IP address of your shop server. The catalog currently provides no images. If it provides some later, your browser loads them on this admin page only, from theme.michael-gahn.de; the server then sees your IP address. Visitors of your shop are not affected.',
            recommended: 'Recommended',
            free: 'Free',
            paid: 'Paid',
            announced: 'Coming soon',
            version: 'Version',
            download: 'Download ZIP',
            details: 'Learn more',
            source: 'Source code on GitHub',
            newTab: '(opens in a new tab)'
        }
    });

    Shopware.Component.register('mgd-barrierefrei-catalog-page', {
        template: [
            '<sw-page class="mgd-barrierefrei-catalog">',
            '  <template #smart-bar-header><h2>{{ $tc("mgd-barrierefrei-catalog.title") }}</h2></template>',
            '  <template #smart-bar-actions>',
            '    <sw-button :disabled="isLoading" @click="load(true)">{{ $tc("mgd-barrierefrei-catalog.refresh") }}</sw-button>',
            '  </template>',
            '  <template #content>',
            '    <sw-card-view>',
            '      <sw-card :title="$tc(\'mgd-barrierefrei-catalog.title\')">',
            '        <p class="mgd-cat-intro">{{ $tc("mgd-barrierefrei-catalog.intro") }}</p>',
            '        <p v-if="isLoading" role="status">{{ $tc("mgd-barrierefrei-catalog.loading") }}</p>',
            '        <p v-else-if="!available" class="mgd-cat-empty" role="status">{{ $tc("mgd-barrierefrei-catalog.unavailable") }}</p>',
            '        <template v-else>',
            '          <p v-if="stale" class="mgd-cat-empty" role="status">{{ $tc("mgd-barrierefrei-catalog.stale") }}</p>',
            '          <p v-if="items.length === 0" class="mgd-cat-empty">{{ $tc("mgd-barrierefrei-catalog.empty") }}</p>',
            '          <div v-else class="mgd-cat-grid">',
            '            <article v-for="item in items" :key="item.id" class="mgd-cat-item" :style="accentStyle(item)">',
            '              <img v-if="isImage(item.imageUrl)" class="mgd-cat-image" :src="item.imageUrl" :alt="item.imageAlt" loading="lazy" referrerpolicy="no-referrer">',
            '              <div class="mgd-cat-head">',
            '                <img v-if="isImage(item.iconUrl)" class="mgd-cat-icon" :src="item.iconUrl" alt="" width="36" height="36" loading="lazy" referrerpolicy="no-referrer">',
            '                <span v-else class="mgd-cat-icon mgd-cat-icon--fallback" aria-hidden="true">{{ item.name.charAt(0) }}</span>',
            '                <h3>{{ item.name }}</h3>',
            '              </div>',
            '              <ul class="mgd-cat-badges">',
            '                <li v-if="item.recommended" class="mgd-cat-badge mgd-cat-badge--rec">{{ $tc("mgd-barrierefrei-catalog.recommended") }}</li>',
            '                <li class="mgd-cat-badge">{{ item.tier === "free" ? $tc("mgd-barrierefrei-catalog.free") : $tc("mgd-barrierefrei-catalog.paid") }}</li>',
            '                <li v-if="item.status === \'announced\'" class="mgd-cat-badge">{{ $tc("mgd-barrierefrei-catalog.announced") }}</li>',
            '                <li v-if="item.version" class="mgd-cat-badge">{{ $tc("mgd-barrierefrei-catalog.version") }} {{ item.version }}</li>',
            '              </ul>',
            '              <p class="mgd-cat-text">{{ item.description }}</p>',
            '              <p class="mgd-cat-links">',
            '                <a v-if="isLink(item.downloadUrl)" :href="item.downloadUrl" target="_blank" rel="noopener noreferrer">{{ $tc("mgd-barrierefrei-catalog.download") }} <span class="sr-only">{{ $tc("mgd-barrierefrei-catalog.newTab") }}</span></a>',
            '                <a v-if="isLink(item.detailsUrl)" :href="item.detailsUrl" target="_blank" rel="noopener noreferrer">{{ $tc("mgd-barrierefrei-catalog.details") }} <span class="sr-only">{{ $tc("mgd-barrierefrei-catalog.newTab") }}</span></a>',
            '                <a v-if="isLink(item.repositoryUrl)" :href="item.repositoryUrl" target="_blank" rel="noopener noreferrer">{{ $tc("mgd-barrierefrei-catalog.source") }} <span class="sr-only">{{ $tc("mgd-barrierefrei-catalog.newTab") }}</span></a>',
            '              </p>',
            '            </article>',
            '          </div>',
            '          <p v-if="updatedText" class="mgd-cat-note">{{ $tc("mgd-barrierefrei-catalog.updatedAt") }} {{ updatedText }}</p>',
            '        </template>',
            '        <p class="mgd-cat-note">{{ $tc("mgd-barrierefrei-catalog.privacy") }}</p>',
            '      </sw-card>',
            '    </sw-card-view>',
            '  </template>',
            '</sw-page>'
        ].join('\n'),

        inject: ['loginService'],

        data: function () {
            return { isLoading: true, available: false, stale: false, items: [], fetchedAt: null };
        },

        computed: {
            updatedText: function () {
                if (!this.fetchedAt) {
                    return '';
                }
                var locale = Shopware.State.get('session').currentLocale || 'de-DE';

                return new Date(this.fetchedAt * 1000).toLocaleString(locale);
            }
        },

        created: function () {
            addStyle();
            this.load(false);
        },

        methods: {
            // Feed holen, Fehler zeigen wir nur als Hinweis
            load: function (refresh) {
                var vm = this;
                var client = Shopware.Application.getContainer('init').httpClient;
                vm.isLoading = true;

                return client.post('/_action/mgd-barrierefrei/catalog', { refresh: refresh === true }, {
                    headers: { Authorization: 'Bearer ' + vm.loginService.getToken(), 'Content-Type': 'application/json' }
                }).then(function (response) {
                    var data = response && response.data ? response.data : {};
                    vm.available = data.available === true && Array.isArray(data.items);
                    vm.items = vm.available ? data.items : [];
                    vm.stale = data.stale === true;
                    vm.fetchedAt = typeof data.fetchedAt === 'number' ? data.fetchedAt : null;
                }).catch(function () {
                    vm.available = false;
                    vm.items = [];
                }).then(function () {
                    vm.isLoading = false;
                });
            },

            isLink: function (url) {
                return startsWithAny(url, LINK_PREFIXES);
            },

            isImage: function (url) {
                return typeof url === 'string' && url.indexOf(IMAGE_PREFIX) === 0;
            },

            // Farbe nur wenn genau #rrggbb, als CSS-Variable (nur für den Rand, Text bleibt dunkel)
            accentStyle: function (item) {
                return typeof item.accent === 'string' && /^#[0-9a-f]{6}$/.test(item.accent) ? { '--mgd-accent': item.accent } : null;
            }
        }
    });

    Shopware.Module.register('mgd-barrierefrei-catalog', {
        type: 'plugin',
        name: 'mgd-barrierefrei-catalog',
        title: 'mgd-barrierefrei-catalog.menuTitle',
        description: 'mgd-barrierefrei-catalog.description',
        color: '#b91c1c',
        icon: 'regular-cog',

        routes: {
            index: {
                component: 'mgd-barrierefrei-catalog-page',
                path: 'index',
                meta: { parentPath: 'sw.settings.index.plugins', privilege: 'system_config:read' }
            }
        },

        // Einstellungen > Erweiterungen
        settingsItem: [{
            name: 'mgd-barrierefrei-catalog',
            to: 'mgd.barrierefrei.catalog.index',
            label: 'mgd-barrierefrei-catalog.menuTitle',
            group: 'plugins',
            icon: 'regular-cog',
            privilege: 'system_config:read'
        }]
    });
})();
