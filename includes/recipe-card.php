<?php

if (isset($rezept)):
    $isPlaceholder = (isset($rezept['recipe_id']) && $rezept['recipe_id'] === 'placeholder');

    if ($isPlaceholder) {
        $cardLink  = 'create_recipe.php';
        $cardTitle = 'Eigener Drink?';
        $cardImg   = 'resources/images/ui/erstelle_jetzt.png';
    } else {
        $cardLink  = 'recipe.php?id=' . $rezept['recipe_id'];
        $cardTitle = $rezept['recipe_name'];
        $cardImg   = !empty($rezept['image_path']) ? $rezept['image_path'] : 'resources/images/placeholders/platzhalter2.png';
    }
?>

<div class="col-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch">
    <div class="card recipe-card shadow-sm rounded-4 overflow-hidden w-100 d-flex flex-column">

        <a href="<?= $cardLink ?>" class="text-decoration-none d-flex flex-column h-100">
            
            <div class="ratio ratio-1x1 bg-light">
                <img src="<?= htmlspecialchars($cardImg) ?>" 
                     class="w-100 h-100 object-fit-cover"
                     alt="<?= htmlspecialchars($cardTitle) ?>"
                     onerror="this.src='resources/images/placeholders/platzhalter2.png';">
            </div>

            <div class="card-body p-3 text-center d-flex flex-column justify-content-center flex-grow-1">
                <h6 class="fw-bold mb-0 recipe-card-title">
                    <?= htmlspecialchars($cardTitle) ?>
                </h6>

                <?php if (!$isPlaceholder && isset($rezept['avg_rating']) && $rezept['avg_rating'] > 0): ?>
                    <div class="text-warning mt-1 small">
                        <i class="bi bi-star-fill"></i> <?= number_format($rezept['avg_rating'], 1) ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>

        <?php if (isset($showControls) && $showControls === true && !$isPlaceholder): ?>
            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                <div class="d-flex gap-2">
                    <a href="edit_recipe.php?id=<?= $rezept['recipe_id'] ?>" 
                       class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill fw-bold btn-edit-small">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    
                    <a href="util/delete_recipe.php?id=<?= $rezept['recipe_id'] ?>" 
                       class="btn btn-sm btn-outline-danger rounded-circle shadow-sm btn-delete-circle"
                       onclick="return confirm('Wirklich löschen?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php endif; ?>