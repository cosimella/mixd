/**
 * MIXD - Haupt-JavaScript für Rezept-Interaktionen
 */

document.addEventListener('DOMContentLoaded', function() {

    // --- TEIL 1: REZEPT ERSTELLEN (create_recipe.php) ---
    const recipeForm = document.getElementById('recipeForm');
    const ingredientContainer = document.getElementById('ingredient-input-container');

    if (recipeForm && ingredientContainer) {
        
        // Funktion global verfügbar machen für onclick
        window.addIngredientRow = function(data = {amount: '', unit: 'cl', name: ''}) {
            let newRow = document.createElement('div');
            newRow.className = 'row g-2 mb-2 ingredient-row';
            newRow.innerHTML = `
                <div class="col-3"><input type="number" step="0.1" name="ing_amount[]" class="form-control bg-light border-0 ing-amount" value="${data.amount}" placeholder="Menge"></div>
                <div class="col-3">
                    <select name="ing_unit[]" class="form-select bg-light border-0 ing-unit">
                        <option value="cl" ${data.unit === 'cl' ? 'selected' : ''}>cl</option>
                        <option value="ml" ${data.unit === 'ml' ? 'selected' : ''}>ml</option>
                        <option value="oz" ${data.unit === 'oz' ? 'selected' : ''}>oz</option>
                        <option value="Stück" ${data.unit === 'Stück' ? 'selected' : ''}>Stück</option>
                    </select>
                </div>
                <div class="col-6"><input type="text" name="ing_name[]" class="form-control bg-light border-0 ing-name" value="${data.name}" placeholder="Zutat"></div>
            `;
            ingredientContainer.appendChild(newRow);
        };

        const saveToLocal = () => {
            const ingredients = [];
            document.querySelectorAll('.ingredient-row').forEach(row => {
                ingredients.push({
                    amount: row.querySelector('.ing-amount').value,
                    unit: row.querySelector('.ing-unit').value,
                    name: row.querySelector('.ing-name').value
                });
            });
            const draft = {
                name: document.getElementById('recipe_name').value,
                description: document.getElementById('description').value,
                steps: document.getElementById('steps').value,
                ingredients: ingredients
            };
            localStorage.setItem('recipeDraft', JSON.stringify(draft));
        };

        const loadFromLocal = () => {
            const raw = localStorage.getItem('recipeDraft');
            if (!raw) { window.addIngredientRow(); return; }
            const draft = JSON.parse(raw);
            document.getElementById('recipe_name').value = draft.name || '';
            document.getElementById('description').value = draft.description || '';
            document.getElementById('steps').value = draft.steps || '';
            if (draft.ingredients && draft.ingredients.length > 0) {
                draft.ingredients.forEach(ing => window.addIngredientRow(ing));
            } else { window.addIngredientRow(); }
        };

        recipeForm.addEventListener('input', saveToLocal);
        loadFromLocal();
    }


    // --- TEIL 2: REZEPT BEARBEITEN (edit_recipe.php) ---
    const editZutatenContainer = document.getElementById('zutaten-liste');

    if (editZutatenContainer) {
        
        window.addNewIngredientRow = function() {
            let rowWrapper = document.createElement('div');
            rowWrapper.className = 'row g-2 mb-2 align-items-center zutat-reihe';
            rowWrapper.innerHTML = `
                <div class="col-2"><input type="number" step="0.1" name="amount[]" class="form-control border-0 bg-light"></div>
                <div class="col-3">
                    <select name="unit[]" class="form-select border-0 bg-light">
                        <option value="cl">cl</option>
                        <option value="ml">ml</option>
                        <option value="Stück">Stück</option>
                        <option value="BL">BL</option>
                    </select>
                </div>
                <div class="col-6"><input type="text" name="ingredient[]" class="form-control border-0 bg-light"></div>
                <div class="col-1 text-end">
                    <button type="button" class="btn text-danger p-0" onclick="this.closest('.zutat-reihe').remove()">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            editZutatenContainer.appendChild(rowWrapper);
        };
    }
});