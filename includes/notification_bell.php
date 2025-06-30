<?php
// File: includes/notification_bell.php
// Notification Bell Component

include_once('notifications.php');

// Create notifications table if it doesn't exist
createNotificationsTable($conn);

// Get unread notification count
$unread_count = getUnreadNotificationCount($conn, $user_id, $user_type);

// Get recent notifications
$notifications = getNotifications($conn, $user_id, $user_type, 5);
?>

<style>
.notification-bell {
    position: relative;
    display: inline-block;
    margin-left: 20px;
}

.notification-icon {
    font-size: 1.5rem;
    color: #00ffff;
    cursor: pointer;
    transition: all 0.3s ease;
    text-shadow: 0 0 10px #00ffff;
}

.notification-icon:hover {
    color: #ff00ff;
    text-shadow: 0 0 15px #ff00ff;
    transform: scale(1.1);
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #ff00ff;
    color: #000;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: bold;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    width: 350px;
    max-height: 400px;
    background: rgba(0, 0, 0, 0.95);
    border: 2px solid #00ffff;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
    backdrop-filter: blur(10px);
    z-index: 1000;
    display: none;
    overflow-y: auto;
}

.notification-dropdown.show {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.notification-header {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(0, 255, 255, 0.3);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.notification-title {
    color: #00ffff;
    font-weight: 600;
    font-size: 1.1rem;
    text-shadow: 0 0 5px #00ffff;
}

.mark-all-read {
    background: none;
    border: 1px solid #00ffff;
    color: #00ffff;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mark-all-read:hover {
    background: #00ffff;
    color: #000;
}

.notification-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.notification-item:hover {
    background: rgba(0, 255, 255, 0.1);
}

.notification-item.unread {
    background: rgba(255, 0, 255, 0.1);
    border-left: 3px solid #ff00ff;
}

.notification-item.unread:hover {
    background: rgba(255, 0, 255, 0.2);
}

.notification-content {
    margin-bottom: 5px;
}

.notification-title-text {
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 3px;
}

.notification-message {
    color: #ccc;
    font-size: 0.8rem;
    line-height: 1.3;
}

.notification-time {
    color: #666;
    font-size: 0.7rem;
}

.notification-type {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    margin-right: 8px;
}

.type-info { background: #00ffff; }
.type-success { background: #28a745; }
.type-warning { background: #ffc107; }
.type-error { background: #dc3545; }

.notification-empty {
    padding: 30px 20px;
    text-align: center;
    color: #666;
}

.notification-empty .icon {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #00ffff;
    text-shadow: 0 0 10px #00ffff;
}

@media (max-width: 768px) {
    .notification-dropdown {
        width: 300px;
        right: -50px;
    }
}
</style>

<div class="notification-bell">
    <div class="notification-icon" onclick="toggleNotifications()">
        🔔
        <?php if ($unread_count > 0): ?>
            <div class="notification-badge"><?php echo $unread_count; ?></div>
        <?php endif; ?>
    </div>
    
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <div class="notification-title">Notifications</div>
            <?php if ($unread_count > 0): ?>
                <button class="mark-all-read" onclick="markAllAsRead()">Mark All Read</button>
            <?php endif; ?>
        </div>
        
        <div class="notification-list">
            <?php if ($notifications->num_rows > 0): ?>
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" 
                         onclick="markAsRead(<?php echo $notification['id']; ?>)">
                        <div class="notification-content">
                            <div class="notification-title-text">
                                <span class="notification-type type-<?php echo $notification['type']; ?>"></span>
                                <?php echo htmlspecialchars($notification['title']); ?>
                            </div>
                            <div class="notification-message">
                                <?php echo htmlspecialchars($notification['message']); ?>
                            </div>
                            <div class="notification-time">
                                <?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="notification-empty">
                    <div class="icon">🔔</div>
                    <div>No notifications yet</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
}

function markAsRead(notificationId) {
    // AJAX call to mark notification as read
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the notification item
            const notificationItem = event.target.closest('.notification-item');
            notificationItem.classList.remove('unread');
            
            // Update badge count
            updateNotificationBadge();
        }
    });
}

function markAllAsRead() {
    // AJAX call to mark all notifications as read
    fetch('mark_all_notifications_read.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread class from all items
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('unread');
            });
            
            // Update badge count
            updateNotificationBadge();
        }
    });
}

function updateNotificationBadge() {
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        const unreadItems = document.querySelectorAll('.notification-item.unread').length;
        if (unreadItems === 0) {
            badge.style.display = 'none';
        } else {
            badge.textContent = unreadItems;
        }
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const bell = document.querySelector('.notification-bell');
    const dropdown = document.getElementById('notificationDropdown');
    
    if (!bell.contains(event.target)) {
        dropdown.classList.remove('show');
    }
});

// Auto-refresh notifications every 30 seconds
setInterval(function() {
    // You can add AJAX call here to refresh notifications
    // For now, we'll just update the badge count
    updateNotificationBadge();
}, 30000);
</script> 