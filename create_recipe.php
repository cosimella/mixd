<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <?php if ($isUploadSuccessful): ?>
                <div class="card shadow-sm border-0 rounded-4 p-5 text-center">
                    <i class="bi bi-stars text-warning display-1"></i>
                    <h2 class="fw-bold mt-3">Abgeschickt!</h2>
                    <p class="text-muted">Dein Rezept ist nun online.</p>
                    <a href="index.php" class="btn btn-primary rounded-pill px-4 mt-3">Zur Startseite</a>
                    </div>
            <?php else: ?>

            <div class="card shadow-sm border-0 rounded-4 p-4 mb-4">
                <h2 class="fw-bold mb-4">Neuer Drink</h2>
                <form id="recipeForm" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="small fw-bold text-muted">NAME DES DRINKS</label>
                        <input type="text" name="recipe_name" id="recipe_name" class="form-control border-0 bg-light py-2" required>
                    </div>
                    <div class="mb-4">
                        <label class="small fw-bold text-muted">STORY / BESCHREIBUNG</label>
                        <textarea name="description" id="description" class="form-control border-0 bg-light" rows="2"></textarea>
                    </div>

                    <div class="mb-4 p-3 bg-white border rounded-4">
                        <label class="small fw-bold text-muted mb-3 d-block">ZUTATEN</label>
                        <div id="ingredient-input-container">
                            </div>
                        <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="addIngredientRow()">+ Weitere Zutat</button>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted">ZUBEREITUNG</label>
                        <textarea name="steps" id="steps" class="form-control border-0 bg-light" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow">VERÖFFENTLICHEN</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include "includes/footer.php"; ?>
</body>
</html>