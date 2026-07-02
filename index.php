<?php
require 'db.php';

$today          = date('Y-m-d');
$today_display  = date('l, d F Y');

// The 9 fixed tasks that should exist every single day
$fixed_tasks = [
    "University Study",
    "Web Development",
    "AI/ML Learning",
    "GYM",
    "Reading Books",
    "Documentation",
    "Research Paper",
    "Projects"
];

// Make sure today's row exists for every fixed task (auto-create once per day)
foreach ($fixed_tasks as $task) {
    $check = $conn->prepare("SELECT id FROM tasks WHERE task_name = ? AND task_date = ? AND is_fixed = 1");
    $check->bind_param("ss", $task, $today);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO tasks (task_name, task_date, is_fixed) VALUES (?, ?, 1)");
        $insert->bind_param("ss", $task, $today);
        $insert->execute();
    }
    $check->close();
}

// Active tasks (pending + in_progress) for TODAY — these show in the 3-column grid
$active_tasks = [];
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_date = ? AND status != 'completed' ORDER BY is_fixed DESC, id ASC");
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $active_tasks[] = $row;
}

// Missed tasks: fixed tasks from PREVIOUS days that were never completed.
// These are surfaced as carry-over cards so nothing silently disappears.
$missed_tasks = [];
$stmt_missed = $conn->prepare("SELECT * FROM tasks WHERE task_date < ? AND status != 'completed' AND is_fixed = 1 ORDER BY task_date ASC, id ASC");
$stmt_missed->bind_param("s", $today);
$stmt_missed->execute();
$result_missed = $stmt_missed->get_result();
while ($row = $result_missed->fetch_assoc()) {
    $missed_tasks[] = $row;
}

// Helper: build the "X tasks remaining" label with the missed date
function missed_label($task_name, $task_date) {
    $formatted = date('d M Y', strtotime($task_date));
    return htmlspecialchars($task_name) . " tasks remaining (" . $formatted . ")";
}

// Completed tasks today — these only show in the sidebar summary
$completed_tasks = [];
$stmt2 = $conn->prepare("SELECT * FROM tasks WHERE task_date = ? AND status = 'completed' ORDER BY end_time DESC");
$stmt2->bind_param("s", $today);
$stmt2->execute();
$result2 = $stmt2->get_result();
while ($row = $result2->fetch_assoc()) {
    $completed_tasks[] = $row;
}

$total_today     = count($active_tasks) + count($completed_tasks);
$completed_count = count($completed_tasks);
$percent         = $total_today > 0 ? round(($completed_count / $total_today) * 100) : 0;

// Helper: turn start/end datetime into a readable HH:MM:SS duration
function calc_duration($start, $end) {
    $diff = strtotime($end) - strtotime($start);
    if ($diff < 0) $diff = 0;
    $h = floor($diff / 3600);
    $m = floor(($diff % 3600) / 60);
    $s = $diff % 60;
    return sprintf("%02d:%02d:%02d", $h, $m, $s);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daily TODO</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page">

    <header class="header">
        <div class="header-top">
            <div class="header-left">
                <p class="eyebrow">Daily Tracker</p>
                <h1>Today's Tasks</h1>
            </div>
            <div class="header-right">
                <p class="date"><?php echo $today_display; ?></p>
                <p class="clock" id="clock">--:--:--</p>
            </div>
        </div>

        <div class="progress-wrap">
            <div class="progress-label">
                <span><?php echo $completed_count; ?> / <?php echo $total_today; ?> tasks completed today</span>
                <span><?php echo $percent; ?>%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
            </div>
        </div>
    </header>

    <div class="layout">

        <main class="main">

            <?php if (!empty($missed_tasks)): ?>
            <section class="missed-section">
                <h2 class="missed-title">⚠ Missed From Previous Days</h2>
                <div class="task-grid missed-grid">
                    <?php foreach ($missed_tasks as $task): ?>
                        <div class="task-card status-<?php echo $task['status']; ?> missed-card">
                            <div class="status-bar"></div>
                            <div class="task-body">
                                <div class="task-top">
                                    <span class="task-name"><?php echo missed_label($task['task_name'], $task['task_date']); ?></span>
                                    <?php if ($task['status'] === 'in_progress'): ?>
                                        <span class="badge badge-progress">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge badge-missed">Missed</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($task['status'] === 'in_progress'): ?>
                                    <p class="task-meta">Started at <?php echo date('h:i A', strtotime($task['start_time'])); ?></p>
                                <?php endif; ?>

                                <div class="task-actions">
                                    <form method="POST" action="start_task.php">
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="btn start-btn" <?php echo $task['status'] !== 'pending' ? 'disabled' : ''; ?>>Start</button>
                                    </form>

                                    <form method="POST" action="end_task.php">
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="btn end-btn" <?php echo $task['status'] !== 'in_progress' ? 'disabled' : ''; ?>>End</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>

            <section class="add-task">
                <form method="POST" action="add_task.php" class="add-task-form">
                    <input type="text" name="task_name" placeholder="Add a new task for today..." required maxlength="100">
                    <button type="submit" class="btn add-btn">+ Add Task</button>
                </form>
            </section>

            <section class="task-grid">
                <?php foreach ($active_tasks as $task): ?>
                    <div class="task-card status-<?php echo $task['status']; ?>">
                        <div class="status-bar"></div>
                        <div class="task-body">
                            <div class="task-top">
                                <span class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></span>
                                <?php if ($task['status'] === 'in_progress'): ?>
                                    <span class="badge badge-progress">In Progress</span>
                                <?php else: ?>
                                    <span class="badge badge-pending">Pending</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($task['status'] === 'in_progress'): ?>
                                <p class="task-meta">Started at <?php echo date('h:i A', strtotime($task['start_time'])); ?></p>
                            <?php endif; ?>

                            <div class="task-actions">
                                <form method="POST" action="start_task.php">
                                    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn start-btn" <?php echo $task['status'] !== 'pending' ? 'disabled' : ''; ?>>Start</button>
                                </form>

                                <form method="POST" action="end_task.php">
                                    <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                    <button type="submit" class="btn end-btn" <?php echo $task['status'] !== 'in_progress' ? 'disabled' : ''; ?>>End</button>
                                </form>

                                <?php if ($task['is_fixed'] == 0): ?>
                                    <form method="POST" action="delete_task.php" onsubmit="return confirm('Delete this task?');">
                                        <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                        <button type="submit" class="btn delete-btn" title="Delete task">&times;</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($active_tasks)): ?>
                    <p class="empty-state">All tasks completed today. Nice work — check the summary panel.</p>
                <?php endif; ?>
            </section>
        </main>

        <aside class="sidebar">
            <h2 class="sidebar-title">Today's Summary</h2>
            <p class="sidebar-stat"><?php echo $completed_count; ?> of <?php echo $total_today; ?> done</p>

            <ul class="summary-list">
                <?php if (empty($completed_tasks)): ?>
                    <li class="summary-empty">Nothing completed yet — get started!</li>
                <?php endif; ?>

                <?php foreach ($completed_tasks as $c): ?>
                    <li class="summary-item">
                        <span class="summary-name"><?php echo htmlspecialchars($c['task_name']); ?></span>
                        <span class="summary-duration"><?php echo calc_duration($c['start_time'], $c['end_time']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

    </div>

</div>

<script src="script.js"></script>
</body>
</html>
