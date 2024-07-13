<?php require __DIR__.'/../partials/header.php' ?>
    <main class="container">
        <div class="py-5"></div>

        <div class="mb-3 d-flex justify-content-between align-items-baseline">
            <h1 class="mb-0">Edit Event</h1>
            <a class="btn btn-outline-primary" href="/home.php">
                <i class="bi bi-house-fill"></i>
                Back to home
            </a>
        </div>

        <form action="/events/update.php" method="post" x-data="eventForm()">
            <input type="hidden" name="eventId" value="<?= $event['id'] ?>"/>
            <div class="row g-3 align-items-center mb-2">
                <div class="col-3">
                    <label for="timeMin" class="form-label">
                        Date From:
                        <span class="fw-bold text-secondary" style="font-size: 0.7rem">(UTC)</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input @change="validateTimeMin" x-model="timeMin" required id="timeMin" name="timeMin"
                           type="datetime-local" class="form-control"/>
                </div>
                <div class="col-3">
                    <label for="timeMax" class="form-label">
                        Date To:
                        <span class="fw-bold text-secondary" style="font-size: 0.7rem">(UTC)</span>
                        <span class="text-danger">*</span>
                    </label>
                    <input @change="validateTimeMax" x-model="timeMax" required id="timeMax" name="timeMax"
                           type="datetime-local" class="form-control"/>
                </div>
                <div class="col-4"></div>
                <div class="col-2">
                    <label class="form-check-label" for="status">
                        Status:<span class="text-danger">*</span>
                    </label>
                    <select required class="form-select" name="status" id="status">
                        <?php
                        $selectedValue = $event['status'] ?? null
                        ?>
                        <option value="" <?= !$event['status'] ? 'selected' : '' ?>>None</option>
                        <option value="confirmed"
                            <?= $event['status'] === 'confirmed' ? 'selected' : '' ?>
                        >
                            Confirmed
                        </option>
                        <option value="tentative" <?= $event['status'] === 'tentative' ? 'selected' : '' ?>>Tentative
                        </option>
                        <option value="cancelled" <?= $event['status'] === '500' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="summary" class="form-label">Summary:<span class="text-danger">*</span></label>
                <input required value="<?= htmlspecialchars($event['summary']) ?>" id="summary" name="summary"
                       type="text" class="form-control"/>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea rows="10" id="description" name="description" type="text"
                          class="form-control"><?= htmlspecialchars($event['description'] ?? '') ?></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-lg btn-primary">
                    Submit
                </button>
            </div>
        </form>
    </main>
    <script>
        function eventForm() {
            return {
                timeMin: "<?= htmlspecialchars($event['timeMin'] ?? '') ?>",
                timeMax: "<?= htmlspecialchars($event['timeMax'] ?? '') ?>",
                errorMessage: '',

                validateTimeMin() {
                    if (!this.validDateTimes()) {
                        alert('Start time must be smaller than end time.');
                        this.timeMin = '';
                    }
                },
                validateTimeMax() {
                    if (!this.validDateTimes()) {
                        alert('End time must be greater than start time.');
                        this.timeMax = '';
                    }
                },
                validDateTimes() {
                    if (!this.timeMin || !this.timeMax) {
                        return true;
                    }
                    return new Date(this.timeMin) <= new Date(this.timeMax);
                },
            };
        }
    </script>
<?php require __DIR__.'/../partials/footer.php' ?>