<table class="table table-hover table-light">
    <thead class="table-dark">
    <tr>
        <th scope="col">Summary</th>
        <th scope="col" class="text-center">Status</th>
        <th scope="col" class="text-center">Start</th>
        <th scope="col" class="text-center">End</th>
        <th scope="col" class="text-center">Organizer</th>
        <th scope="col" class="text-center"></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($eventsList as $event): ?>
        <tr>
            <td title="<?= htmlspecialchars($event['summary']) ?>">
                <span class="d-inline-block event-summary">
                    <?= htmlspecialchars($event['summary']) ?>
                </span>
            </td>
            <td class="text-center">
                <?php
                $statusClass = '';
                switch ($event['status']) {
                    case 'confirmed':
                        $statusClass = 'badge text-bg-success';
                        break;
                    case 'tentative':
                        $statusClass = 'badge text-bg-warning';
                        break;
                    case 'cancelled':
                        $statusClass = 'badge text-bg-danger';
                        break;
                    default:
                        $statusClass = 'badge text-bg-secondary';
                        break;
                }
                ?>
                <span class="<?= $statusClass ?>">
                        <?= htmlspecialchars($event['status']) ?>
                    </span>
            </td>
            <td class="text-center">
                <?= date('F j, Y \a\t g:i A', strtotime($event['start']['dateTime'])) ?>
            </td>
            <td class="text-center">
                <?= date('F j, Y \a\t g:i A', strtotime($event['end']['dateTime'])) ?>
            </td>
            <td class="text-center">
                <?php if ($event['organizer']['self']): ?>
                    <span class="badge text-bg-secondary text-uppercase">self</span>
                <?php else: ?>
                    <?= htmlspecialchars($event['organizer']['email']) ?>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <div class="btn-group-sm">
                    <a title="Event link" target="_blank" href="<?= htmlspecialchars($event['htmlLink']) ?>"
                       class="btn btn-sm btn-outline-info">
                        <i class="bi bi-eye"></i>
                    </a>
                    <form class="d-inline-block" action="/events/edit.php" method="post">
                        <input type="hidden" name="eventId" value="<?= $event['id'] ?>">
                        <button title="Edit event" type="submit" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    </form>
                    <form class="d-inline-block" action="/events/delete.php" method="post">
                        <input type="hidden" name="eventId" value="<?= $event['id'] ?>">
                        <button title="Delete event" type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>