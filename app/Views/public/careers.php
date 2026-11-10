<section class="section">
    <div class="container" style="max-width:860px;">
        <div class="section-header">
            <h1>Careers at <?= e($settings['restaurant_name'] ?? 'Renzo') ?></h1>
        </div>

        <div class="form-panel" style="margin-bottom:24px;">
            <p style="margin-top:0;font-size:1.05rem;">
                <?= e($settings['careers_intro'] ?? 'We are always looking for passionate chefs, servers, and hospitality professionals to join the Renzo kitchen and dining room.') ?>
            </p>
            <p class="muted">
                If you take pride in warm hospitality, thoughtful ingredients, and craft-driven service, we would love to hear from you.
            </p>
            <?php $careersEmail = trim((string) ($settings['careers_email'] ?? 'neezaamee@gmail.com')) ?: 'neezaamee@gmail.com'; ?>
            <a href="mailto:<?= e($careersEmail) ?>?subject=Renzo%20Career%20Application" class="btn btn-gold">
                Email your CV to <?= e($careersEmail) ?>
            </a>
        </div>

        <div class="grid-3">
            <article class="card">
                <div class="card-body">
                    <h3 class="card-title" style="color:var(--gold);">Culinary Team</h3>
                    <p class="muted" style="margin:0;">Line cooks, prep chefs, and hearth specialists committed to seasonal wood-fired cuisine.</p>
                </div>
            </article>
            <article class="card">
                <div class="card-body">
                    <h3 class="card-title" style="color:var(--gold);">Front of House</h3>
                    <p class="muted" style="margin:0;">Hosts, servers, and dining room leads who create memorable guest experiences.</p>
                </div>
            </article>
            <article class="card">
                <div class="card-body">
                    <h3 class="card-title" style="color:var(--gold);">Beverage &amp; Bar</h3>
                    <p class="muted" style="margin:0;">Bartenders and sommeliers crafting balanced zero-proof and house pairings.</p>
                </div>
            </article>
        </div>
    </div>
</section>
