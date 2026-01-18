<?php
if (isset($rezept)):

    $isCreateNewPlaceholder = (isset($rezept['recipe_id']) && $rezept['recipe_id'] === 'placeholder');

    if ($isCreateNewPlaceholder) {
        $cardDestinationLink = isset($_SESSION['userid']) ? 'create_recipe.php' : 'login.php';
        $displayTitle = 'Eigener Drink?';
        $displayThumbnail = 'resources/images/ui/erstelle_jetzt.png';
        $cardSpecialStyling = 'rezeptkarte-placeholder';
    } else {
        $cardDestinationLink = 'recipe.php?id=' . $rezept['recipe_id'];
        $displayTitle = $rezept['recipe_name'];
        $displayThumbnail = !empty($rezept['image_path']) ? $rezept['image_path'] : 'resources/images/placeholders/platzhalter2.png';
        $cardSpecialStyling = '';
    }
?>

<div class="col-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch">
    <div class="rezeptkarte bg-white rounded-4 shadow-sm overflow-hidden w-100 d-flex flex-column <?php echo $cardSpecialStyling; ?>">

        <a href="<?php echo $cardDestinationLink; ?>" class="text-decoration-none text-dark d-flex flex-column h-100">
            <div class="ratio ratio-1x1 bg-light">
                <img src="<?php echo htmlspecialchars($displayThumbnail); ?>" 
                     class="w-100 h-100 object-fit-cover"
                     alt="<?php echo htmlspecialchars($displayTitle); ?>"
                     onerror="this.src='resources/images/placeholders/platzhalter2.png';">
            </div>

            <div class="card-body p-3 text-center d-flex flex-column justify-content-center flex-grow-1">
                <h6 class="fw-bold mb-1">
                    <?php echo htmlspecialchars($displayTitle); ?>
                </h6>

                <?php if (isset($rezept['avg_rating']) && $rezept['avg_rating'] > 0 && !$isCreateNewPlaceholder): ?>
                    <div class="text-warning" style="font-size: 0.8rem;">
                        <i class="bi bi-star-fill"></i> <?php echo number_format($rezept['avg_rating'], 1); ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>

        <?php if (isset($showControls) && $showControls === true && !$isCreateNewPlaceholder): ?>
            <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
                <div class="d-flex gap-2 align-items-center">
                    <a href="edit_recipe.php?id=<?php echo $rezept['recipe_id']; ?>"
                       class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill py-1 shadow-sm fw-bold"
                       style="font-size: 0.75rem;">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                    
                    <a href="util/delete_recipe.php?id=<?php echo $rezept['recipe_id']; ?>"
                       class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center rounded-circle shadow-sm"
                       style="width: 32px; height: 32px; flex-shrink: 0;"
                       onclick="return confirm('Möchtest du dieses Rezept wirklich löschen? Alle Bilder und Kommentare dazu werden entfernt.')">
                        <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>