{{--
    Reusable photo picker. Replaces the plain <input type="file" multiple>
    with a friendly drop-zone + live thumbnail previews + per-file remove
    buttons.

    Usage:
        @include('partials.photo-picker', [
            'name'  => 'images',
            'max'   => 3,
            'hint'  => 'First photo becomes the cover.',
        ])

    Optional params:
        $accept       (default: image/jpeg,image/png,image/webp)
        $hint         (small grey help text under the zone)
        $error        (a $message string from @error('images'))
        $maxSizeMb    (default: 5)
--}}
@php
    $pickerId    = $name . '_picker_' . uniqid();
    $accept      = $accept    ?? 'image/jpeg,image/png,image/webp';
    $hint        = $hint      ?? null;
    $error       = $error     ?? null;
    $maxSizeMb   = $maxSizeMb ?? 5;
@endphp

<div class="gp-pic" data-gp-pic data-gp-pic-max="{{ $max }}" data-gp-pic-max-mb="{{ $maxSizeMb }}">
    <label for="{{ $pickerId }}" class="gp-pic__zone" tabindex="0">
        <div class="gp-pic__icon">
            <i class="fas fa-cloud-arrow-up"></i>
        </div>
        <div class="gp-pic__primary">
            <strong>Tap to add photos</strong>
            <span class="gp-pic__or"> or drag &amp; drop here</span>
        </div>
        <div class="gp-pic__sub">
            JPG, PNG, or WEBP &middot; up to {{ $maxSizeMb }} MB each &middot;
            <span data-gp-pic-counter>0 / {{ $max }} selected</span>
        </div>
    </label>

    <input type="file"
           id="{{ $pickerId }}"
           name="{{ $name }}[]"
           class="gp-pic__input @if($error) is-invalid @endif"
           accept="{{ $accept }}"
           multiple
           data-gp-pic-input>

    @if ($error)
        <small class="text-danger d-block mt-1">{{ $error }}</small>
    @endif
    @if ($hint)
        <small class="text-muted d-block mt-1">{{ $hint }}</small>
    @endif

    <div class="gp-pic__previews" data-gp-pic-previews hidden></div>
</div>

@once
<style>
    .gp-pic { position: relative; }
    .gp-pic__input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        overflow: hidden;
        z-index: -1;
    }

    .gp-pic__zone {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        text-align: center;
        padding: 1.6rem 1.25rem;
        border: 2px dashed rgba(212, 160, 60, .55);
        border-radius: 14px;
        background: linear-gradient(180deg, #fffdf6, #fdf5e6);
        color: #1a1208;
        font-family: 'Inter', system-ui, sans-serif;
        cursor: pointer;
        transition: background .2s ease, border-color .2s ease, transform .12s ease;
        margin: 0;
    }
    .gp-pic__zone:hover,
    .gp-pic__zone:focus-visible {
        border-color: #d4a03c;
        background: linear-gradient(180deg, #fdf5e6, #fbecc4);
        outline: none;
    }
    .gp-pic--dragover .gp-pic__zone {
        border-color: #d4a03c;
        background: linear-gradient(180deg, #fbecc4, #f3c779);
        transform: scale(1.01);
    }
    .gp-pic--full .gp-pic__zone {
        opacity: .55;
        cursor: not-allowed;
        background: #f9fafb;
        border-color: #e5e7eb;
    }
    .gp-pic--full .gp-pic__zone:hover {
        border-color: #e5e7eb;
        background: #f9fafb;
    }

    .gp-pic__icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(212, 160, 60, .15);
        color: #b8872e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .gp-pic__primary {
        font-size: 1rem;
    }
    .gp-pic__primary strong { color: #b8872e; }
    .gp-pic__or { color: #6b7280; font-weight: 400; }
    .gp-pic__sub {
        font-size: .82rem;
        color: #6b7280;
    }

    .gp-pic__previews {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: .65rem;
        margin-top: .75rem;
    }
    .gp-pic__previews:not([hidden]) {
        display: grid;
    }

    .gp-pic__thumb {
        position: relative;
        aspect-ratio: 4 / 3;
        border-radius: 10px;
        overflow: hidden;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .06);
    }
    .gp-pic__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .gp-pic__thumb__remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(0, 0, 0, .55);
        color: #fff;
        border: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        line-height: 1;
        transition: background .15s ease, transform .15s ease;
    }
    .gp-pic__thumb__remove:hover {
        background: #dc2626;
        transform: scale(1.08);
    }
    .gp-pic__thumb__cover {
        position: absolute;
        bottom: 4px;
        left: 4px;
        background: linear-gradient(135deg, #d4a03c, #b8872e);
        color: #1a1208;
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 2px 7px;
        border-radius: 999px;
    }
    .gp-pic__thumb__name {
        position: absolute;
        bottom: 4px;
        right: 4px;
        background: rgba(0, 0, 0, .55);
        color: #fff;
        font-size: .68rem;
        padding: 2px 6px;
        border-radius: 4px;
        max-width: 65%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<script>
    (function () {
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-gp-pic]').forEach(initPicker);
        });

        function initPicker(root) {
            var input    = root.querySelector('[data-gp-pic-input]');
            var zone     = root.querySelector('.gp-pic__zone');
            var previews = root.querySelector('[data-gp-pic-previews]');
            var counter  = root.querySelector('[data-gp-pic-counter]');
            var max      = parseInt(root.dataset.gpPicMax || '6', 10);
            var maxBytes = (parseInt(root.dataset.gpPicMaxMb || '5', 10)) * 1024 * 1024;

            // Use DataTransfer so we can edit input.files programmatically.
            var bag = new DataTransfer();

            function syncInput() {
                input.files = bag.files;
                render();
            }

            function addFiles(filesLike) {
                var added = 0, rejected = [];
                for (var i = 0; i < filesLike.length; i++) {
                    var f = filesLike[i];
                    if (! f.type || ! /^image\//.test(f.type))     { rejected.push(f.name + ' — not an image'); continue; }
                    if (f.size > maxBytes)                          { rejected.push(f.name + ' — over ' + (maxBytes / 1024 / 1024) + ' MB'); continue; }
                    if (bag.files.length >= max)                    { rejected.push(f.name + ' — slot full'); continue; }
                    // De-dupe by name + size + lastModified
                    var dup = false;
                    for (var j = 0; j < bag.files.length; j++) {
                        var g = bag.files[j];
                        if (g.name === f.name && g.size === f.size && g.lastModified === f.lastModified) {
                            dup = true; break;
                        }
                    }
                    if (dup) continue;
                    bag.items.add(f);
                    added++;
                }
                syncInput();
                if (rejected.length) {
                    alert("Couldn't add:\n• " + rejected.join('\n• '));
                }
            }

            function removeAt(idx) {
                var keep = [];
                for (var i = 0; i < bag.files.length; i++) {
                    if (i !== idx) keep.push(bag.files[i]);
                }
                bag = new DataTransfer();
                keep.forEach(function (f) { bag.items.add(f); });
                syncInput();
            }

            function render() {
                var n = bag.files.length;
                counter.textContent = n + ' / ' + max + ' selected';
                root.classList.toggle('gp-pic--full', n >= max);

                previews.innerHTML = '';
                if (n === 0) { previews.hidden = true; return; }
                previews.hidden = false;

                for (var i = 0; i < bag.files.length; i++) (function (idx, file) {
                    var url = URL.createObjectURL(file);
                    var fig = document.createElement('div');
                    fig.className = 'gp-pic__thumb';
                    fig.innerHTML =
                        '<img src="' + url + '" alt="">' +
                        (idx === 0 ? '<span class="gp-pic__thumb__cover">Cover</span>' : '') +
                        '<span class="gp-pic__thumb__name">' + escapeHtml(file.name) + '</span>' +
                        '<button type="button" class="gp-pic__thumb__remove" aria-label="Remove">&times;</button>';
                    fig.querySelector('.gp-pic__thumb__remove').addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        URL.revokeObjectURL(url);
                        removeAt(idx);
                    });
                    previews.appendChild(fig);
                })(i, bag.files[i]);
            }

            function escapeHtml(s) {
                return String(s).replace(/[&<>"']/g, function (c) {
                    return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
                });
            }

            // Browse via the hidden <input>
            input.addEventListener('change', function (e) {
                // Capture the user's freshly picked files BEFORE we overwrite input.files.
                var picked = Array.from(e.target.files || []);
                // The browser already overwrote input.files with the picked set.
                // We want to MERGE into our bag rather than replace, so rebuild:
                var existing = [];
                for (var i = 0; i < bag.files.length; i++) existing.push(bag.files[i]);
                bag = new DataTransfer();
                existing.forEach(function (f) { bag.items.add(f); });
                addFiles(picked);
                // Clear the input's transient picker selection so the same file
                // can be re-picked later if the user removes it.
                e.target.value = '';
            });

            // Drag and drop on the zone
            ['dragenter', 'dragover'].forEach(function (ev) {
                zone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    root.classList.add('gp-pic--dragover');
                });
            });
            ['dragleave', 'drop'].forEach(function (ev) {
                zone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    root.classList.remove('gp-pic--dragover');
                });
            });
            zone.addEventListener('drop', function (e) {
                if (e.dataTransfer && e.dataTransfer.files) {
                    addFiles(e.dataTransfer.files);
                }
            });

            // Keyboard activation of the zone — Enter / Space triggers the file picker
            zone.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    input.click();
                }
            });

            render();
        }
    })();
</script>
@endonce
