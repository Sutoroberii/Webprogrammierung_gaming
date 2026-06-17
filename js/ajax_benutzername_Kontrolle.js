document.addEventListener("DOMContentLoaded", function () {
    const usernameInput = document.getElementById("username");
    const message = document.getElementById("username-check-message");
    const form = document.querySelector("form");

    let timer = null;
    let usernameState = "unknown";

    usernameInput.addEventListener("input", function () {
        clearTimeout(timer);

        const username = usernameInput.value.trim();
        usernameState = "unknown";

        if (username === "") {
            message.textContent = "";
            return;
        }

        timer = setTimeout(function () {
            fetch("check-username.php?username=" + encodeURIComponent(username))
                .then(response => response.json())
                .then(data => {
                    message.textContent = data.message;

                    if (data.available) {
                        message.style.color = "green";
                        usernameState = "available";
                    } else {
                        message.style.color = "red";
                        usernameState = "unavailable";
                    }
                })
                .catch(() => {
                    message.textContent = "Prüfung fehlgeschlagen";
                    message.style.color = "red";
                    usernameState = "unknown";
                });
        }, 300);
    });

    form.addEventListener("submit", function (event) {
        if (usernameState === "unavailable") {
            event.preventDefault();
            message.textContent = "Benutzername schon vergeben.";
            message.style.color = "red";
        }
    });
});