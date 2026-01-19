function toggleSettings() {
    const box = document.getElementById("moreSettings");
    const arrow = document.getElementById("arrow");

    if (box.style.display === "block") {
        box.style.display = "none";
        arrow.style.transform = "rotate(0deg)";
    } else {
        box.style.display = "block";
        arrow.style.transform = "rotate(183deg)";
    }
}

// Edit day count
document.getElementById("editDays").addEventListener("click", () => {
    let span = document.getElementById("daysBox");
    let current = span.textContent.trim();

    let input = document.createElement("input");
    input.value = current;
    input.style.width = "50px";

    span.replaceWith(input);
    input.focus();

    input.addEventListener("blur", () => {
        let newSpan = document.createElement("span");
        newSpan.id = "daysBox";
        newSpan.textContent = input.value;
        input.replaceWith(newSpan);
        document.getElementById("days").value = input.value; // update hidden input
    });
});
