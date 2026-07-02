// Live clock in the header. This is purely visual — it does NOT control
// task timing. Task start/end times are recorded by PHP/MySQL when you
// click Start or End, so they stay accurate even if you close the tab.

function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString();
}

updateClock();
setInterval(updateClock, 1000);
