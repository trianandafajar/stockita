<x-app-layout>
    @php $isBuyer = $role === 'buyer'; @endphp

    <div class="min-h-screen space-y-6">
        {{-- Header --}}
        <div class="bg-white/80 backdrop-blur-md shadow-sm border border-green-200 rounded-2xl">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div class="size-14 rounded-2xl bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 flex items-center justify-center shadow-md">
                            <i data-lucide="sparkles" class="size-7 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">AI Insights</h1>
                            <p class="text-gray-500 text-sm">
                                @if ($isBuyer)
                                    Personalized product recommendations powered by Groq AI
                                @else
                                    Real-time business analytics powered by data &amp; Groq AI
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if (!$apiReady)
                            <div class="bg-amber-50 border border-amber-300 text-amber-900 px-4 py-2 rounded-xl text-sm flex items-center gap-2">
                                <i data-lucide="alert-triangle" class="size-4"></i>
                                <span><strong>GROQ_API_KEY</strong> not set in <code>.env</code></span>
                            </div>
                        @endif

                        <button type="button"
                                id="refresh-all-btn"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm font-semibold shadow-sm transition disabled:opacity-60 disabled:cursor-not-allowed">
                            <i data-lucide="refresh-cw" id="refresh-all-icon" class="size-4"></i>
                            <span id="refresh-all-label">Refresh All</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cards --}}
        @if ($isBuyer)
            <x-ai-card id="personalized"
                       endpoint="{{ route('ai-insights.personalized') }}"
                       title="Product Recommendations For You"
                       subtitle="AI selects products from the store catalog based on your purchase history"
                       icon="shopping-cart"
                       accent="green" />
        @else
            <x-ai-card id="recommendations"
                       endpoint="{{ route('ai-insights.recommendations') }}"
                       title="Business Recommendations"
                       subtitle="30-day performance summary + priority actions"
                       icon="lightbulb"
                       accent="green" />

            <x-ai-card id="topProducts"
                       endpoint="{{ route('ai-insights.top-products') }}"
                       title="Top Product Evaluation"
                       subtitle="Top 10 products with AI verdicts &amp; comments"
                       icon="flame"
                       accent="amber" />

            <x-ai-card id="stockSuggestions"
                       endpoint="{{ route('ai-insights.stock-suggestions') }}"
                       title="Restock Suggestions"
                       subtitle="Automatic classification: urgent / suggested / overstock"
                       icon="package"
                       accent="sky" />
        @endif

        <p class="text-xs text-gray-400 text-center pb-6 flex items-center justify-center gap-2">
            <i data-lucide="zap" class="size-3"></i>
            Powered by Groq <code>{{ config('services.groq.model') }}</code> · stored in DB · auto-refresh every 10 minutes
        </p>
    </div>

    {{-- Chart.js & Lucide --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <script>
        (function () {
            const API_READY = @json($apiReady);

            // helpers
            const rp  = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const num = (n) => Number(n || 0).toLocaleString('id-ID');
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) =>
                ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

            const icon = (name, cls = 'size-4') =>
                `<i data-lucide="${name}" class="${cls}"></i>`;

            const refreshIcons = () => {
                if (window.lucide) window.lucide.createIcons();
            };

            const charts = {};
            const destroyChart = (id) => {
                if (charts[id]) { charts[id].destroy(); delete charts[id]; }
            };

            // ─── RENDERER: recommendations ───
            function renderRecommendations(out, data) {
                const m = data.metrics;
                const priorityClass = {
                    high:   'bg-rose-100 text-rose-700 border-rose-200',
                    medium: 'bg-amber-100 text-amber-700 border-amber-200',
                    low:    'bg-gray-100 text-gray-700 border-gray-200',
                };
                const priorityLabel = { high: 'High', medium: 'Medium', low: 'Low' };

                out.innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            ${statCard('Total Revenue', rp(m.revenue_total_rp), 'emerald', '30 days')}
                            ${statCard('Total Orders',  num(m.total_orders),    'sky',     '30 days')}
                            ${statCard('Avg Order',     rp(m.avg_order_rp),     'violet',  'per transaction')}
                            ${statCard('Low Stock',     num(m.low_stock_count), m.low_stock_count > 0 ? 'rose' : 'gray', '≤ 10 units')}
                        </div>

                        ${data.ai_summary ? `
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                                <div class="size-9 rounded-lg bg-green-600 text-white flex items-center justify-center flex-shrink-0">
                                    ${icon('message-square-text', 'size-5')}
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed flex-1 pt-1">${esc(data.ai_summary)}</p>
                            </div>
                        ` : ''}

                        <div class="bg-white border border-gray-100 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                ${icon('trending-up', 'size-4 text-emerald-600')}
                                30-Day Revenue Trend
                            </h4>
                            <canvas id="chart-rec-trend" height="80"></canvas>
                        </div>

                        ${data.ai_actions && data.ai_actions.length ? `
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    ${icon('target', 'size-4 text-green-600')}
                                    Priority Actions
                                </h4>
                                <div class="space-y-2">
                                    ${data.ai_actions.map((a, i) => `
                                        <div class="flex items-start gap-3 p-4 bg-white border border-gray-200 rounded-xl hover:border-green-300 transition">
                                            <div class="size-8 rounded-full bg-green-100 text-green-700 font-bold flex items-center justify-center text-sm flex-shrink-0">${i + 1}</div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h5 class="font-semibold text-gray-800">${esc(a.title)}</h5>
                                                    <span class="text-xs px-2 py-0.5 rounded-full border ${priorityClass[a.priority] || priorityClass.medium}">
                                                        ${priorityLabel[a.priority] || 'Medium'}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">${esc(a.reason)}</p>
                                                ${a.related_products && a.related_products.length ? `
                                                    <div class="flex flex-wrap gap-1 mt-2">
                                                        ${a.related_products.map(p => `<span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded">${esc(p)}</span>`).join('')}
                                                    </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${productListBlock('Top 5 Best-Sellers', 'trophy', 'text-amber-500', data.top_products, 'units_sold', 'units', 'emerald')}
                            ${productListBlock('Slow Movers', 'turtle', 'text-gray-500', data.slow_movers, 'units_sold', 'units', 'gray')}
                        </div>

                        ${data.low_stock && data.low_stock.length ? `
                            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-rose-800 mb-2 flex items-center gap-2">
                                    ${icon('alert-triangle', 'size-4')}
                                    Low Stock Warning
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    ${data.low_stock.slice(0, 12).map(s => `
                                        <span class="text-xs bg-white border border-rose-200 px-3 py-1 rounded-full">
                                            ${esc(s.name)} · <strong class="text-rose-700">${s.qty}</strong>
                                        </span>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;

                destroyChart('rec-trend');
                const ctx = document.getElementById('chart-rec-trend');
                if (ctx) {
                    charts['rec-trend'] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.revenue_trend.map(d => d.label),
                            datasets: [{
                                label: 'Revenue',
                                data: data.revenue_trend.map(d => d.revenue),
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 2,
                                pointRadius: 0,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: (ctx) => rp(ctx.raw) } },
                            },
                            scales: { y: { ticks: { callback: (v) => 'Rp ' + (v / 1000).toFixed(0) + 'k' } } },
                        },
                    });
                }
            }

            // ─── RENDERER: top-products ───
            function renderTopProducts(out, data) {
                const verdictBadge = {
                    star:   { label: 'Star',   ico: 'star',            cls: 'bg-amber-100 text-amber-800 border-amber-300' },
                    stable: { label: 'Stable', ico: 'check-circle-2',  cls: 'bg-emerald-100 text-emerald-800 border-emerald-300' },
                    risk:   { label: 'Risk',   ico: 'alert-octagon',   cls: 'bg-rose-100 text-rose-800 border-rose-300' },
                };

                out.innerHTML = `
                    <div class="space-y-6">
                        ${data.ai_summary ? `
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                                <div class="size-9 rounded-lg bg-amber-500 text-white flex items-center justify-center flex-shrink-0">
                                    ${icon('message-square-text', 'size-5')}
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed flex-1 pt-1">${esc(data.ai_summary)}</p>
                            </div>
                        ` : ''}

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div class="bg-white border border-gray-100 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    ${icon('bar-chart-3', 'size-4 text-amber-600')}
                                    Units Sold (Top 10)
                                </h4>
                                <canvas id="chart-tp-units" height="220"></canvas>
                            </div>
                            <div class="bg-white border border-gray-100 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                    ${icon('pie-chart', 'size-4 text-amber-600')}
                                    Category Distribution
                                </h4>
                                <canvas id="chart-tp-cat" height="220"></canvas>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                ${icon('trophy', 'size-4 text-amber-500')}
                                Best-Selling Products
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                ${data.products.map((p, i) => {
                                    const v = verdictBadge[p.verdict] || verdictBadge.stable;
                                    return `
                                        <div class="bg-white border border-gray-200 rounded-xl p-4 hover:border-amber-300 transition">
                                            <div class="flex items-start justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                    <span class="text-xs font-bold text-gray-400 w-6">#${i + 1}</span>
                                                    <h5 class="font-semibold text-gray-800 truncate">${esc(p.name)}</h5>
                                                </div>
                                                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full border ${v.cls} whitespace-nowrap">
                                                    ${icon(v.ico, 'size-3')} ${v.label}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 mb-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1"><span class="size-2 rounded-full bg-amber-400"></span>${esc(p.category)}</span>
                                                <span>·</span>
                                                <span>${num(p.units_sold)} units</span>
                                                <span>·</span>
                                                <span>${p.share_pct}%</span>
                                            </div>
                                            <div class="text-xs font-semibold text-emerald-700 mb-2">${rp(p.revenue_rp)}</div>
                                            ${p.ai_comment ? `<p class="text-xs text-gray-600 italic border-l-2 border-amber-300 pl-2">${esc(p.ai_comment)}</p>` : ''}
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </div>
                `;

                destroyChart('tp-units');
                destroyChart('tp-cat');

                const colors = ['#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ef4444', '#06b6d4', '#ec4899', '#84cc16', '#f97316', '#6366f1'];

                const ctxU = document.getElementById('chart-tp-units');
                if (ctxU) {
                    charts['tp-units'] = new Chart(ctxU, {
                        type: 'bar',
                        data: {
                            labels: data.products.map(p => p.name),
                            datasets: [{ label: 'Units', data: data.products.map(p => p.units_sold), backgroundColor: '#f59e0b', borderRadius: 6 }],
                        },
                        options: {
                            indexAxis: 'y',
                            plugins: { legend: { display: false } },
                            scales: { y: { ticks: { font: { size: 11 } } } },
                        },
                    });
                }

                const ctxC = document.getElementById('chart-tp-cat');
                if (ctxC && data.category_breakdown.length) {
                    charts['tp-cat'] = new Chart(ctxC, {
                        type: 'doughnut',
                        data: {
                            labels: data.category_breakdown.map(c => c.category),
                            datasets: [{
                                data: data.category_breakdown.map(c => c.units),
                                backgroundColor: colors,
                                borderWidth: 2,
                                borderColor: '#fff',
                            }],
                        },
                        options: {
                            plugins: {
                                legend: { position: 'bottom', labels: { font: { size: 11 } } },
                                tooltip: { callbacks: { label: (ctx) => `${ctx.label}: ${num(ctx.raw)} units` } },
                            },
                            cutout: '60%',
                        },
                    });
                }
            }

            // ─── RENDERER: stock-suggestions ───
            function renderStockSuggestions(out, data) {
                const c = data.counts;
                out.innerHTML = `
                    <div class="space-y-6">
                        <div class="grid grid-cols-3 gap-3">
                            ${statCard('Urgent',     num(c.urgent),     'rose',  'critical stock')}
                            ${statCard('Suggested',  num(c.suggested),  'amber', 'running low')}
                            ${statCard('Overstock',  num(c.overstock),  'sky',   'slow-moving')}
                        </div>

                        ${stockSection('Urgent Restock',          'siren',          'text-rose-600',  data.urgent,    'rose',  true)}
                        ${stockSection('Suggested Restock',       'clipboard-list', 'text-amber-600', data.suggested, 'amber', true)}
                        ${stockSection('Overstock — Consider Promotion', 'archive',  'text-sky-600',  data.overstock, 'sky',   false)}
                    </div>
                `;
            }

            function stockSection(title, iconName, iconColor, items, accent, showSuggestedQty) {
                if (!items || !items.length) {
                    return `
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                ${icon(iconName, 'size-4 ' + iconColor)} ${title}
                            </h4>
                            <div class="text-sm text-gray-400 italic px-4 py-3 bg-gray-50 rounded-xl">No items in this category.</div>
                        </div>
                    `;
                }

                const cls = {
                    rose:  { bar: 'bg-rose-500',  ring: 'border-rose-200',  bg: 'bg-rose-50' },
                    amber: { bar: 'bg-amber-500', ring: 'border-amber-200', bg: 'bg-amber-50' },
                    sky:   { bar: 'bg-sky-500',   ring: 'border-sky-200',   bg: 'bg-sky-50' },
                }[accent];

                return `
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            ${icon(iconName, 'size-4 ' + iconColor)} ${title}
                            <span class="text-gray-400 font-normal">(${items.length})</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            ${items.map(it => {
                                const cover = it.days_of_cover;
                                const coverLabel = cover === null ? 'no sales yet' : `~${cover} days cover`;
                                const pct = cover === null ? 0 : Math.min(100, (cover / 30) * 100);
                                return `
                                    <div class="${cls.bg} border ${cls.ring} rounded-xl p-4">
                                        <div class="flex items-start justify-between gap-2 mb-2">
                                            <h5 class="font-semibold text-gray-800 text-sm">${esc(it.product)}</h5>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">${coverLabel}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-xs mb-2 flex-wrap">
                                            <span class="text-gray-600">Stock: <strong class="text-gray-900">${num(it.current_qty)}</strong></span>
                                            <span class="text-gray-400">·</span>
                                            <span class="text-gray-600">${it.avg_per_day}/day</span>
                                            ${showSuggestedQty && it.suggested_qty ? `
                                                <span class="text-gray-400">·</span>
                                                <span class="text-emerald-700 font-semibold inline-flex items-center gap-1">
                                                    ${icon('shopping-bag', 'size-3')} Buy ~${num(it.suggested_qty)}
                                                </span>
                                            ` : ''}
                                        </div>
                                        <div class="w-full bg-white/60 rounded-full h-1.5 overflow-hidden mb-2">
                                            <div class="${cls.bar} h-full rounded-full" style="width: ${pct}%"></div>
                                        </div>
                                        ${it.ai_reason ? `<p class="text-xs text-gray-600 italic">${esc(it.ai_reason)}</p>` : ''}
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            // ─── RENDERER: personalized ───
            function renderPersonalized(out, data) {
                if (data.empty) {
                    out.innerHTML = `
                        <div class="text-center py-8 text-gray-500 flex flex-col items-center gap-3">
                            ${icon('shopping-bag', 'size-10 text-gray-300')}
                            <p class="text-sm">${esc(data.message)}</p>
                        </div>
                    `;
                    return;
                }

                out.innerHTML = `
                    <div class="space-y-6">
                        ${data.ai_summary ? `
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                                <div class="size-9 rounded-lg bg-green-600 text-white flex items-center justify-center flex-shrink-0">
                                    ${icon('message-square-text', 'size-5')}
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed flex-1 pt-1">${esc(data.ai_summary)}</p>
                            </div>
                        ` : ''}

                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                ${icon('sparkles', 'size-4 text-green-600')}
                                Recommended For You
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                ${data.recommendations.map(r => `
                                    <div class="bg-white border border-green-200 rounded-xl p-4 hover:shadow-md transition">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">${esc(r.category)}</span>
                                            <span class="text-emerald-700 font-bold">${rp(r.price_rp)}</span>
                                        </div>
                                        <h5 class="font-semibold text-gray-800 mb-2">${esc(r.name)}</h5>
                                        ${r.ai_reason ? `<p class="text-xs text-gray-600 italic">${esc(r.ai_reason)}</p>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>

                        ${data.history && data.history.length ? `
                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                    ${icon('history', 'size-4 text-gray-500')}
                                    Your Purchase History
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    ${data.history.slice(0, 10).map(h => `
                                        <span class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded-full">
                                            ${esc(h.product)} <span class="text-gray-400">×${h.units}</span>
                                        </span>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            // ─── shared partials ───
            function statCard(label, value, accent, sub) {
                const map = {
                    emerald: 'from-emerald-50 to-emerald-100 text-emerald-700 border-emerald-200',
                    sky:     'from-sky-50 to-sky-100 text-sky-700 border-sky-200',
                    violet:  'from-violet-50 to-violet-100 text-violet-700 border-violet-200',
                    rose:    'from-rose-50 to-rose-100 text-rose-700 border-rose-200',
                    amber:   'from-amber-50 to-amber-100 text-amber-700 border-amber-200',
                    gray:    'from-gray-50 to-gray-100 text-gray-700 border-gray-200',
                };
                return `
                    <div class="bg-gradient-to-br ${map[accent]} border rounded-xl p-4">
                        <p class="text-xs font-medium uppercase tracking-wider opacity-75">${label}</p>
                        <p class="text-xl md:text-2xl font-bold mt-1">${value}</p>
                        ${sub ? `<p class="text-xs opacity-60 mt-1">${sub}</p>` : ''}
                    </div>
                `;
            }

            function productListBlock(title, iconName, iconColor, items, valueKey, unit, accent) {
                if (!items || !items.length) {
                    return `
                        <div class="bg-white border border-gray-100 rounded-xl p-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                ${icon(iconName, 'size-4 ' + iconColor)} ${title}
                            </h4>
                            <p class="text-sm text-gray-400 italic">No data available yet.</p>
                        </div>
                    `;
                }
                const max = Math.max(...items.map(x => x[valueKey] || 0)) || 1;
                const barCls = accent === 'emerald' ? 'bg-emerald-400' : 'bg-gray-300';

                return `
                    <div class="bg-white border border-gray-100 rounded-xl p-4">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            ${icon(iconName, 'size-4 ' + iconColor)} ${title}
                        </h4>
                        <div class="space-y-2">
                            ${items.map(it => {
                                const pct = ((it[valueKey] || 0) / max) * 100;
                                return `
                                    <div>
                                        <div class="flex items-center justify-between text-xs mb-1">
                                            <span class="font-medium text-gray-700 truncate flex-1">${esc(it.name)}</span>
                                            <span class="text-gray-500 ml-2">${num(it[valueKey])} ${unit}</span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                                            <div class="${barCls} h-full rounded-full" style="width: ${pct}%"></div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }

            // ─── dispatcher ───
            const RENDERERS = {
                'recommendations':   renderRecommendations,
                'top-products':      renderTopProducts,
                'stock-suggestions': renderStockSuggestions,
                'personalized':      renderPersonalized,
            };

            const STORAGE_LABEL = (json) => {
                const tag = json.from_storage ? 'from DB' : 'fresh';
                return `${tag} · ${json.generated_at}`;
            };

            async function load(card, refresh = false) {
                const endpoint = card.dataset.endpoint;
                const out      = card.querySelector('[data-output]');
                const refBtn   = card.querySelector('[data-action="refresh"]');
                const stamp    = card.querySelector('[data-stamp]');
                const refIcon  = card.querySelector('[data-card-refresh]');

                refBtn.disabled = true;
                refIcon?.classList.add('animate-spin');
                out.innerHTML = `
                    <div class="flex flex-col items-center justify-center py-12 text-gray-500">
                        <div class="size-10 rounded-full border-4 border-gray-200 border-t-green-500 animate-spin"></div>
                        <p class="text-sm mt-3">AI is analyzing…</p>
                    </div>
                `;
                stamp.textContent = '';

                try {
                    const url = endpoint + (refresh ? '?refresh=1' : '');
                    const res = await fetch(url, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
                    const json = await res.json();

                    if (!json.success) throw new Error(json.error || 'Failed to load insights.');

                    const renderer = RENDERERS[json.type];
                    if (!renderer) throw new Error('Unknown insight type: ' + json.type);

                    renderer(out, json.data);
                    stamp.textContent = STORAGE_LABEL(json);
                    refreshIcons();
                } catch (e) {
                    out.innerHTML = `
                        <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-4 text-sm flex items-center gap-2">
                            ${icon('alert-triangle', 'size-4')} ${esc(e.message)}
                        </div>
                    `;
                    refreshIcons();
                } finally {
                    refBtn.disabled = false;
                    refIcon?.classList.remove('animate-spin');
                }
            }

            // Per-card refresh
            const allCards = Array.from(document.querySelectorAll('[data-ai-card]'));
            allCards.forEach((card) => {
                card.querySelector('[data-action="refresh"]')?.addEventListener('click', () => load(card, true));
            });

            // Global "Refresh All"
            const allBtn   = document.getElementById('refresh-all-btn');
            const allIcon  = document.getElementById('refresh-all-icon');
            const allLabel = document.getElementById('refresh-all-label');

            async function refreshAll(forceRefresh = true) {
                if (!API_READY) return;
                allBtn.disabled = true;
                allIcon.classList.add('animate-spin');
                allLabel.textContent = 'Refreshing…';
                try {
                    await Promise.all(allCards.map((c) => load(c, forceRefresh)));
                } finally {
                    allBtn.disabled = false;
                    allIcon.classList.remove('animate-spin');
                    allLabel.textContent = 'Refresh All';
                }
            }

            allBtn?.addEventListener('click', () => refreshAll(true));

            // Lucide pass for initial chrome
            refreshIcons();

            // Auto-load on first visit (uses DB if fresh, generates if stale/missing)
            if (API_READY) {
                refreshAll(false);
            } else {
                allCards.forEach((card) => {
                    card.querySelector('[data-output]').innerHTML = `
                        <div class="text-center text-gray-400 py-8 text-sm flex flex-col items-center gap-2">
                            ${icon('alert-circle', 'size-8 text-gray-300')}
                            <p>Set <code>GROQ_API_KEY</code> in <code>.env</code> to enable AI insights.</p>
                        </div>
                    `;
                });
                refreshIcons();
            }
        })();
    </script>
</x-app-layout>
