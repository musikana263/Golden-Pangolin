{{-- Drag-and-drop wiring for any admin list table. Add these two attrs:
       <tbody data-sortable-url="{{ route('admin.sort', 'destinations') }}">
         <tr data-id="{{ $row->id }}"> ... <td class="drag-handle">≡</td> </tr>

     This partial loads SortableJS from CDN (~10KB gzipped), then finds every
     [data-sortable-url] container and makes its child rows draggable.
     On drop it POSTs {ids: [...]} to the URL with the CSRF token, and shows
     a small toast so the admin knows the save succeeded. --}}
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js" defer></script>

<style>
    /* Drag handle column. Cursor + subtle hover, gold like the rest of the
       admin accents. */
    .drag-handle {
        cursor: grab;
        color: rgba(184, 135, 46, .55);
        font-size: 1rem;
        user-select: none;
        width: 32px;
        text-align: center;
    }
    .drag-handle:hover { color: var(--admin-primary, #b8872e); }
    .sortable-ghost {
        opacity: .35;
        background: rgba(212, 160, 60, .12) !important;
    }
    .sortable-drag {
        background: #fff;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .15);
    }

    /* Tiny toast that appears bottom-right after a successful reorder. */
    .gp-sort-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #2a1c10;
        color: #fbf3e1;
        padding: 10px 18px;
        border-radius: 10px;
        border: 1px solid rgba(212, 160, 60, .45);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
        font-size: .85rem;
        font-weight: 600;
        z-index: 1090;
        opacity: 0;
        transform: translateY(8px);
        transition: opacity .25s ease, transform .25s ease;
        pointer-events: none;
    }
    .gp-sort-toast i { color: #f3c779; margin-right: .4rem; }
    .gp-sort-toast--err { border-color: #d9534f; }
    .gp-sort-toast--err i { color: #ff8a87; }
    .gp-sort-toast[data-shown="1"] { opacity: 1; transform: translateY(0); }
</style>

<script>
    (function () {
        function toast(msg, isError) {
            var el = document.createElement('div');
            el.className = 'gp-sort-toast' + (isError ? ' gp-sort-toast--err' : '');
            el.innerHTML = '<i class="fas fa-' + (isError ? 'circle-exclamation' : 'check-circle') + '"></i>' + msg;
            document.body.appendChild(el);
            requestAnimationFrame(function () { el.setAttribute('data-shown', '1'); });
            setTimeout(function () {
                el.removeAttribute('data-shown');
                setTimeout(function () { el.remove(); }, 300);
            }, 2200);
        }

        function init() {
            if (typeof Sortable === 'undefined') {
                // SortableJS loaded with defer — try again next tick.
                return setTimeout(init, 50);
            }

            document.querySelectorAll('[data-sortable-url]').forEach(function (container) {
                if (container.dataset.gpSortableInit === '1') return;
                container.dataset.gpSortableInit = '1';

                var url = container.dataset.sortableUrl;
                var csrf = document.querySelector('meta[name="csrf-token"]');

                Sortable.create(container, {
                    handle: '.drag-handle',
                    animation: 160,
                    ghostClass: 'sortable-ghost',
                    dragClass:  'sortable-drag',
                    onEnd: function () {
                        var ids = Array.prototype.slice
                            .call(container.querySelectorAll('[data-id]'))
                            .map(function (el) { return parseInt(el.dataset.id, 10); })
                            .filter(function (n) { return !isNaN(n); });

                        fetch(url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf ? csrf.content : '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ ids: ids }),
                        }).then(function (r) {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.json();
                        }).then(function () {
                            toast('Order saved', false);
                        }).catch(function () {
                            toast('Could not save new order — refresh and try again.', true);
                        });
                    },
                });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
