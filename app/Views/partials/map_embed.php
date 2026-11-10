<?php
$embedUrl = trim((string) ($settings['map_embed_url'] ?? ''));
$hasValidEmbed = $embedUrl !== '' && str_starts_with($embedUrl, 'https://') && filter_var($embedUrl, FILTER_VALIDATE_URL) !== false;
?>
<?php if ($hasValidEmbed): ?>
    <iframe
        class="map-frame"
        title="Renzo Restaurant Location Map"
        src="<?= e($embedUrl) ?>"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
        allowfullscreen
    ></iframe>
<?php else: ?>
    <?php
    $mapHtml = '<!DOCTYPE html><html><body style="margin:0;height:100%;display:flex;align-items:center;justify-content:center;background:#110b08;color:#d4af37;font-family:Georgia,serif;text-align:center;padding:16px;">'
        . '<div><div style="font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#c7b4a2;margin-bottom:6px;">Location Map Placeholder</div>'
        . '<div style="font-size:18px;margin-bottom:4px;">' . htmlspecialchars((string) ($settings['restaurant_name'] ?? 'Renzo'), ENT_QUOTES, 'UTF-8') . '</div>'
        . '<div style="font-size:13px;color:#d6c5b3;">' . htmlspecialchars((string) ($settings['contact_address'] ?? '124 Harbor Lane, Culinary District'), ENT_QUOTES, 'UTF-8') . '</div></div>'
        . '</body></html>';
    ?>
    <iframe
        class="map-frame"
        title="Renzo Restaurant Location Map"
        srcdoc="<?= e($mapHtml) ?>"
        loading="lazy"
    ></iframe>
<?php endif; ?>
