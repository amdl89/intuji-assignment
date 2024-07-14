<form action="/home.php" method="get">
    <div class="row g-3 align-items-center mb-2">
        <div class="col-3">
            <label for="timeMin" class="form-label">Date From:</label>
            <input value="<?= htmlspecialchars($_GET['timeMin'] ?? '') ?>" id="timeMin" name="timeMin" type="date" class="form-control"/>
        </div>
        <div class="col-3">
            <label for="timeMax" class="form-label">Date To:</label>
            <input value="<?= htmlspecialchars($_GET['timeMax'] ?? '') ?>" id="timeMax" name="timeMax" type="date" class="form-control"/>
        </div>
        <div class="col-2">
            <div class="form-check mt-4">
                <input <?= isset($_GET['showDeleted']) ? 'checked' : '' ?> class="form-check-input" type="checkbox" name="showDeleted" value="" id="showDeleted">
                <label class="form-check-label" for="showDeleted">
                    Show Cancelled
                </label>
            </div>
        </div>
        <div class="col-1">
            <label class="form-check-label" for="maxResults">
                Max results:
            </label>
            <select class="form-select" name="maxResults" id="maxResults">
                <?php
                $selectedValue = $_GET['showDeleted'] ?? null
                ?>
                <option value="100" <?=  ($selectedValue === '100' || !$selectedValue) ? 'selected' : '' ?>>100</option>
                <option value="250" <?=  $selectedValue === '250' ? 'selected' : '' ?>>250</option>
                <option value="500" <?=  $selectedValue === '500' ? 'selected' : '' ?>>500</option>
                <option value="1000" <?=  $selectedValue === '1000' ? 'selected' : '' ?>>1000</option>
            </select>
        </div>
        <div class="col-3">
            <div class="d-flex justify-content-end">
                <div class="btn-group-sm mt-4">
                    <a href="/home.php" title="Reset" class="btn btn-sm btn-secondary">
                        Reset
                    </a>
                    <button title="Filter" type="submit" class="btn btn-sm btn-primary">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>