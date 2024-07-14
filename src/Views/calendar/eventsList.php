<?php require __DIR__.'/../partials/header.php' ?>
    <main class="container">
        <div class="py-5"></div>

        <div class="mb-3 d-flex justify-content-between align-items-baseline">
            <h1 class="mb-0">Events</h1>

            <form class="d-inline-block" action="/oauth/revokeCalendarTokens.php" method="post"
                  onsubmit="return confirm('Are you sure you want to disconnect your google calendar?')"
            >
                <button type="submit" class="btn btn-sm btn-danger text-uppercase">
                    <i class="bi bi-calendar-x-fill"></i>
                    Disconnect
                </button>
            </form>
        </div>
        <div x-data="{filterShown: false}" class="py-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <a title="Add new event" href="/events/new.php"
                   class="btn btn-sm btn-primary text-uppercase">
                    <i class="bi bi-calendar-plus"></i>
                    Add
                </a>
                <button
                        @click="filterShown = !filterShown"
                        :class="`btn btn-sm text-uppercase ${filterShown ? 'bg-secondary text-white' : 'btn-outline-secondary'}`"
                >
                    <i class="bi bi-funnel-fill"></i>
                    Filters
                </button>
            </div>
            <div x-show="filterShown" class="mt-2 bg-secondary-subtle p-4 rounded-2">
                <?php require __DIR__.'/../partials/eventsListFilterForm.php' ?>
            </div>
        </div>
        <?php require __DIR__.'/../partials/eventsListTable.php' ?>
    </main>
<?php require __DIR__.'/../partials/footer.php' ?>