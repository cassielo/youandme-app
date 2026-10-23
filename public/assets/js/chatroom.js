// Chatroom page: local send times, and new messages and members every few seconds without reloading
(function () {
    var REFRESH_MS = 3000;
    var box = document.getElementById('chat-messages');
    var members = document.getElementById('chat-members');
    var timer = null;
    var stopped = false;
    var lastMembers = null;
    if (!box) {
        return;
    }

    // Today: "20:56"; earlier this year: "Oct 5, 19:32"; other years: "Oct 9, 2025, 14:05"
    function formatTime(seconds) {
        var d = new Date(seconds * 1000);
        var now = new Date();
        var time = d.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit', hourCycle: 'h23'});
        if (d.toDateString() === now.toDateString()) {
            return time;
        }
        var date = {month: 'short', day: 'numeric'};
        if (d.getFullYear() !== now.getFullYear()) {
            date.year = 'numeric';
        }
        return d.toLocaleDateString('en-US', date) + ', ' + time;
    }

    function showTimes() {
        box.querySelectorAll('.chat-time').forEach(function (el) {
            el.textContent = formatTime(Number(el.getAttribute('data-sent')));
        });
    }

    // Scroll to new messages only when already near the bottom
    function atBottom() {
        return box.scrollHeight - box.scrollTop - box.clientHeight < 30;
    }

    function schedule() {
        clearTimeout(timer);
        if (!stopped && !document.hidden) {
            timer = setTimeout(refresh, REFRESH_MS);
        }
    }

    function refresh() {
        fetch('chatroommessages.php', {credentials: 'same-origin', cache: 'no-store'})
            .then(function (response) {
            var isJson = (response.headers.get('Content-Type') || '').indexOf('application/json') === 0;
            if (!isJson) {
                // Logged out (the server sent the home page): reload to go there
                stopped = true;
                window.location.reload();
                return null;
            }
            if (response.status === 403) {
                stopped = true; // No longer a member: stop
                return null;
            }
            return response.ok ? response.json() : null;   // Other errors: try again next time
        })
            .then(function (data) {
            if (!data) {
                return;
            }
            if (String(data.last) !== box.getAttribute('data-last')) {
                var follow = atBottom();
                box.innerHTML = data.messages;
                box.setAttribute('data-last', data.last);
                showTimes();
                if (follow) {
                    box.scrollTop = box.scrollHeight;
                }
            }
            if (members && data.members !== lastMembers) {
                members.innerHTML = data.members;
                lastMembers = data.members;
            }
        })
            .catch(function () {
            // Network trouble: try again next time
        })
            .then(schedule);
    }

    // Hidden tabs pause; refresh when shown again
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            clearTimeout(timer);
        } else if (!stopped) {
            refresh();
        }
    });
    showTimes();
    box.scrollTop = box.scrollHeight;
    schedule();
})();
