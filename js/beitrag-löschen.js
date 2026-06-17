document.addEventListener("DOMContentLoaded", function () {
    const deleteForms = document.querySelectorAll(".delete-post-form");

    deleteForms.forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const confirmed = confirm("Wollen Sie diesen Beitrag wirklich löschen?");

            if (!confirmed) {
                event.preventDefault();
            }
        });
    });
});