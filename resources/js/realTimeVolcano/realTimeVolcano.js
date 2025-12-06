document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.volcano-activity-page');
    if (!root) return;

    const endpoint   = root.dataset.volcanoEndpoint;
    const continentEl = document.getElementById('volcano-continent-select');
    const limitEl     = document.getElementById('volcano-limit-select');
    const refreshBtn  = document.getElementById('volcano-refresh-btn');

    const loadingEl   = document.getElementById('volcano-loading-state');
    const errorEl     = document.getElementById('volcano-error-state');
    const emptyEl     = document.getElementById('volcano-empty-state');
    const listEl      = document.getElementById('volcano-activity-list');
    const fallbackEl = document.getElementById('volcano-fallback-checkbox');

    if (!endpoint || !continentEl || !limitEl || !refreshBtn || !loadingEl || !errorEl || !emptyEl || !listEl) {
        console.warn('Volcano page elements missing, aborting script.');
        return;
    }

    async function fetchVolcanoData() {
        const continent = continentEl.value;
        const limit     = limitEl.value;
        const useFallback = !fallbackEl || fallbackEl.checked;

        loadingEl.style.display = 'block';
        errorEl.style.display   = 'none';
        emptyEl.style.display   = 'none';
        errorEl.textContent     = '';
        listEl.innerHTML        = '';

        const url = new URL(endpoint, window.location.origin);
        url.searchParams.set('continent', continent);
        url.searchParams.set('limit', limit);
        url.searchParams.set('fallback', useFallback ? '1' : '0');

        try {
            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            });

            const data = await res.json();

            if (!res.ok || data.status === 'error') {
                throw new Error(data.message || 'Failed to load volcano data.');
            }

            const events = data.events ?? [];
            renderEvents(events);
        } catch (err) {
            console.error(err);
            errorEl.textContent = err.message || 'Something went wrong while fetching volcano data.';
            errorEl.style.display = 'block';
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    function renderEvents(events) {
        listEl.innerHTML = '';

        if (!events || !events.length) {
            emptyEl.style.display = 'block';
            return;
        }

        emptyEl.style.display = 'none';

        events.forEach((ev) => {
            const card = document.createElement('article');
            card.className = 'volcano-card volcano-event-card';

            card.innerHTML = `
                <div class="volcano-card-header volcano-event-card-header">
                    <div class="volcano-card-title volcano-event-title">
                        <i class="fa-solid fa-volcano"></i>
                        <span class="volcano-card-title-text volcano-event-title-text">
                            ${escapeHtml(ev.title ?? 'Natural disaster event')}
                        </span>
                    </div>
                    <div class="volcano-card-meta volcano-event-meta">
                        <span>
                            <i class="fas fa-globe-europe"></i>
                            ${escapeHtml(ev.location_label ?? 'Location not specified')}
                        </span>
                        ${ev.date ? `
                            <span>
                                <i class="fas fa-clock"></i>
                                ${escapeHtml(ev.date)}
                            </span>` : ''}
                    </div>
                </div>

                <div class="volcano-card-body volcano-event-body">
                    ${ev.magnitude          ? `<p><strong>Magnitude:</strong> ${escapeHtml(String(ev.magnitude))}</p>` : ''}
                    ${ev.severity           ? `<p><strong>Severity score:</strong> ${escapeHtml(String(ev.severity))}</p>` : ''}
                    ${ev.description        ? `<p>${escapeHtml(ev.description)}</p>` : ''}
                </div>

                <div class="volcano-card-footer volcano-event-footer">
                    <div>
                        ${ev.vei ? `
                            <span class="vei-pill">
                                <i class="fas fa-arrow-up"></i>
                                VEI: ${escapeHtml(String(ev.vei))}
                            </span>` : ''}
                    </div>
                    <div class="volcano-card-footer-right volcano-event-footer-right">
                        ${ev.exposed_population ? `
                            <span class="pop-pill">
                                <i class="fas fa-users"></i>
                                Exposed: ${escapeHtml(String(ev.exposed_population))}</span>` : ''}
                        ${ev.learn_more_url ? `
                            <a href="${ev.learn_more_url}"
                               class="volcano-learn-more"
                               target="_blank"
                               rel="noopener noreferrer">
                                Learn more <i class="fas fa-arrow-up-right-from-square"></i>
                            </a>` : ''}
                    </div>
                </div>
            `;

            listEl.appendChild(card);
        });
    }

    function escapeHtml(value) {
        if (value == null) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    refreshBtn.addEventListener('click', fetchVolcanoData);
    continentEl.addEventListener('change', fetchVolcanoData);
    limitEl.addEventListener('change', fetchVolcanoData);

    fetchVolcanoData();
});
