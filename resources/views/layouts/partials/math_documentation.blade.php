{{-- SIDEBAR DOKUMENTASI MATH (FULL & AMAN UNTUK MATHQUILL) --}}
<div id="math-docs"
     class="fixed top-0 right-0 h-full w-[380px]
            bg-white/95 text-slate-800
            dark:bg-gradient-to-b dark:from-azwara-darkest dark:to-azwara-darker
            dark:text-white
            shadow-2xl border-l border-slate-200/60 dark:border-white/10
            transform translate-x-full
            transition-transform duration-300
            z-50 flex flex-col">

    {{-- HEADER --}}
    <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-white/10">
        <h3 class="font-semibold text-lg tracking-wide">MathQuill · Cheat Sheet</h3>
        <button id="close-docs"
                class="text-slate-500 hover:text-red-500 dark:text-white/70 dark:hover:text-red-400 text-xl">
            ✕
        </button>
    </div>

    {{-- CONTENT --}}
    <div class="flex-1 overflow-y-auto p-4 space-y-3 text-sm scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-white/20">

        {{-- AKAR & PANGKAT --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>√ · ⁿ√ · x² · xᵢ</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>√x <code>\sqrt{x}</code></div>

                {{-- INI YANG PENTING --}}
                <div>
                    ³√x (akar pangkat n)
                    <code>\nthroot{3}{x}</code>
                </div>

                <div>x² <code>x^2</code></div>
                <div>xⁿ <code>x^n</code></div>
                <div>xᵢ <code>x_i</code></div>
                <div>xᵢⱼ <code>x_{i,j}</code></div>

                <div class="text-xs text-slate-500 dark:text-white/60 pt-2">
                    ⚠️ Untuk akar berpangkat, <strong>JANGAN</strong> ketik <code>\sqrt[n]{x}</code> secara manual.
                    Gunakan <code>\nthroot{n}{x}</code>.
                </div>
            </div>
        </details>

        {{-- PECAHAN & DELIMITER --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>a/b · ( ) · |x|</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>a/b <code>\frac{a}{b}</code></div>
                <div>(x) <code>(x)</code></div>
                <div>[x] <code>[x]</code></div>
                <div>{x} <code>\{x\}</code></div>
                <div>|x| <code>|x| (pakai tanda pipa)</code></div>
            </div>
        </details>

        {{-- KALKULUS --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>∑ · ∫ · lim</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>∑ <code>\sum</code></div>
                <div>∑ᵢⁿ <code>\sum_{i=1}^{n}</code></div>
                <div>∫ <code>\int</code></div>
                <div>∫ₐᵇ <code>\int_a^b</code></div>
                <div>lim <code>\lim_{x \to 0}</code></div>
            </div>
        </details>

        {{-- TRIGONOMETRI --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>sin · cos · tan</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>sin x <code>\sin x</code></div>
                <div>cos x <code>\cos x</code></div>
                <div>tan x <code>\tan x</code></div>
                <div>log x <code>\log x</code></div>
                <div>ln x <code>\ln x</code></div>
            </div>
        </details>

        {{-- RELASI --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>= · ≠ · ≤ · ≥</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>= <code>=</code></div>
                <div>≠ <code>\neq</code></div>
                <div>≤ <code>\leq</code></div>
                <div>≥ <code>\geq</code></div>
                <div>≈ <code>\approx</code></div>
            </div>
        </details>

        {{-- SIMBOL UMUM --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>→ · ∞ · π</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>→ <code>\to</code></div>
                <div>∞ <code>\infty</code></div>
                <div>π <code>\pi</code></div>
                <div>θ <code>\theta</code></div>
                <div>α β γ <code>\alpha \beta \gamma</code></div>
            </div>
        </details>

        {{-- HIMPUNAN --}}
        <details class="group bg-slate-100/80 dark:bg-white/5 rounded-xl">
            <summary class="cursor-pointer px-4 py-3 font-semibold flex justify-between items-center">
                <span>ℝ · ℕ · ℤ</span>
                <span class="group-open:rotate-90 transition">›</span>
            </summary>
            <div class="px-4 pb-4 space-y-2">
                <div>ℝ <code>\mathbb{R}</code></div>
                <div>ℕ <code>\mathbb{N}</code></div>
                <div>ℤ <code>\mathbb{Z}</code></div>
                <div>∈ <code>\in</code></div>
                <div>∉ <code>\notin</code></div>
            </div>
        </details>
        {{-- TIPS --}}
        <div
            class="mt-6 p-4 rounded-xl
                bg-slate-100/80 text-slate-700
                dark:bg-white/5 dark:text-gray-300">

            <h4 class="font-semibold mb-2 text-slate-800 dark:text-white">
                ℹ
            </h4>

            <ul class="space-y-1 text-sm">
                <li>
                    • Ketik
                    <code class="px-1 rounded bg-black/5 dark:bg-white/10">\</code>
                    lalu lanjutkan nama simbol
                </li>
                <li>• Gunakan MathQuill hanya untuk rumus</li>
                <li>• Teks biasa tetap diketik di textarea</li>
                <li>• Rumus otomatis dirender di preview</li>
            </ul>
        </div>

    </div>
</div>
