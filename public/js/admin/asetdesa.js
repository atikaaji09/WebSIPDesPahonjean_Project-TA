document.addEventListener("DOMContentLoaded", function () {

    const table = document.querySelector("table");
    if (!table) return;

    document.addEventListener("click", function (e) {

        if (!e.target.classList.contains("btn-save")) return;

        const row = e.target.closest("tr");
        if (!row) return;

        const inputs = row.querySelectorAll("input");

        inputs.forEach(input => {

            const td = input.closest("td");

            if (td && td.dataset.type === "numeric") {

                input.value = input.value.replace(/[^0-9]/g, '');

            }

        });

    });

});